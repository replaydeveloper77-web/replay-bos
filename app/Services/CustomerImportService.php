<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

class CustomerImportService
{
    public function __construct(
        private CustomerService $customerService,
        private AuditService $auditService,
    ) {
    }

    /**
     * Import customers from an uploaded spreadsheet file (xlsx, xls, csv).
     *
     * @return array{
     *     success_count: int,
     *     skipped_count: int,
     *     errors: array<string>,
     *     customers: array<array{name: string, service_number: string, category: string, company: ?string}>
     * }
     */
    public function import(UploadedFile $file, string $defaultCategory, User $actor, Request $request): array
    {
        $defaultCategory = strtoupper(trim($defaultCategory));
        if (! in_array($defaultCategory, Customer::CATEGORIES, true)) {
            $defaultCategory = 'HOME';
        }

        $realPath = $file->getRealPath();
        if (! $realPath || ! file_exists($realPath)) {
            throw new RuntimeException('File spreadsheet tidak ditemukan atau gagal diunggah.');
        }

        try {
            $spreadsheet = IOFactory::load($realPath);
        } catch (\Throwable $e) {
            throw new RuntimeException('Gagal membaca file spreadsheet: '.$e->getMessage());
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        if ($highestRow < 1) {
            return [
                'success_count' => 0,
                'skipped_count' => 0,
                'errors' => ['File spreadsheet kosong atau tidak memiliki data.'],
                'customers' => [],
            ];
        }

        // Read all rows as raw string matrix
        $rawRows = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cell = $worksheet->getCell([$col, $row]);
                $val = $cell->getValue();

                // Format numbers or dates if needed
                if ($val instanceof \Stringable) {
                    $val = (string) $val;
                } elseif (is_float($val) || is_int($val)) {
                    // Avoid scientific notation for phone numbers e.g. 8.12E+11
                    $val = number_format($val, 0, '', '');
                }

                $rowData[] = $val !== null ? trim((string) $val) : '';
            }
            $rawRows[$row] = $rowData;
        }

        // Determine if Row 1 is a header row
        $headerMap = $this->detectHeaders($rawRows[1] ?? []);
        $startRow = 2;

        if (empty($headerMap)) {
            // Row 1 doesn't look like recognized headers. Use positional mapping starting at Row 1
            $headerMap = $this->fallbackPositionalMap();
            $startRow = 1;
        }

        $createdCustomers = [];
        $errors = [];
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            for ($rowIndex = $startRow; $rowIndex <= $highestRow; $rowIndex++) {
                $row = $rawRows[$rowIndex] ?? [];

                // Check if row is completely empty
                $hasContent = false;
                foreach ($row as $cellValue) {
                    if ($cellValue !== '') {
                        $hasContent = true;
                        break;
                    }
                }

                if (! $hasContent) {
                    $skippedCount++;
                    continue;
                }

                // Extract fields based on header map
                $data = $this->mapRowToCustomerData($row, $headerMap, $defaultCategory);

                // Validation: Customer Name is required
                if (empty($data['name'])) {
                    $errors[] = "Baris {$rowIndex}: Nama Pelanggan kosong (wajib diisi).";
                    continue;
                }

                // Category normalization
                $category = strtoupper($data['category'] ?? $defaultCategory);
                if (! in_array($category, Customer::CATEGORIES, true)) {
                    $category = $defaultCategory;
                }
                $data['category'] = $category;

                // Service Number generation:
                // If service_number is blank, CustomerService::create will generate it automatically.
                // If service_number is supplied, check uniqueness to avoid duplicate key issues.
                if (! empty($data['service_number'])) {
                    $exists = Customer::where('service_number', $data['service_number'])->exists();
                    if ($exists) {
                        $errors[] = "Baris {$rowIndex}: Nomor Layanan '{$data['service_number']}' sudah digunakan oleh pelanggan lain.";
                        continue;
                    }
                }

                // Phone / WhatsApp cleanup
                if (! empty($data['whatsapp'])) {
                    $data['whatsapp'] = $this->sanitizePhone($data['whatsapp']);
                }

                // Email validation & sanitation
                if (! empty($data['email'])) {
                    if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                        // Keep note of invalid email format, clear or retain? Let's retain warning
                        $errors[] = "Baris {$rowIndex} ({$data['name']}): Format email '{$data['email']}' tidak valid, dilewati.";
                        $data['email'] = null;
                    }
                }

                // Create customer via CustomerService
                $customer = $this->customerService->create($data, $actor, $request);

                $createdCustomers[] = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'service_number' => $customer->service_number,
                    'category' => $customer->category,
                    'company' => $customer->company,
                    'service' => $customer->service,
                ];
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new RuntimeException('Terjadi kesalahan saat menyimpan data: '.$e->getMessage(), 0, $e);
        }

        return [
            'success_count' => count($createdCustomers),
            'skipped_count' => $skippedCount,
            'errors' => $errors,
            'customers' => $createdCustomers,
        ];
    }

    /**
     * Build header map based on header strings.
     *
     * @param array<int, string> $headerRow
     * @return array<string, int>
     */
    private function detectHeaders(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $index => $header) {
            $normalized = strtolower(trim(preg_replace('/[^a-zA-Z0-9\/_]/', '', $header)));

            if (preg_match('/(namapelanggan|namaclient|namacustomer|pelanggan|^nama$)/i', $normalized) && ! isset($map['name'])) {
                $map['name'] = $index;
            } elseif (preg_match('/(perusahaan|company|kantor|namaperusahaan)/i', $normalized) && ! isset($map['company'])) {
                $map['company'] = $index;
            } elseif (preg_match('/(namapic|picname|^pic$|kontakpic)/i', $normalized) && ! isset($map['pic_name'])) {
                $map['pic_name'] = $index;
            } elseif (preg_match('/(whatsapp|nowa|wa|telepon|notelepon|nohp|phone|hp)/i', $normalized) && ! isset($map['whatsapp'])) {
                $map['whatsapp'] = $index;
            } elseif (preg_match('/(email|surel|mail)/i', $normalized) && ! isset($map['email'])) {
                $map['email'] = $index;
            } elseif (preg_match('/(lokasialamat|lokasi|alamat|location|address)/i', $normalized) && ! isset($map['location'])) {
                $map['location'] = $index;
            } elseif (preg_match('/(layanan|service|paketlayanan|^paket$)/i', $normalized) && ! isset($map['service'])) {
                $map['service'] = $index;
            } elseif (preg_match('/(kategori|category|tipe|jenis)/i', $normalized) && ! isset($map['category'])) {
                $map['category'] = $index;
            } elseif (preg_match('/(nomorlayanan|nolayanan|servicenumber|idlayanan)/i', $normalized) && ! isset($map['service_number'])) {
                $map['service_number'] = $index;
            } elseif (preg_match('/(hargalangganan|hargabulanan|harga|tarif|subscriptionprice)/i', $normalized) && ! isset($map['subscription_price'])) {
                $map['subscription_price'] = $index;
            } elseif (preg_match('/(catatan|keterangan|notes|keterangan)/i', $normalized) && ! isset($map['notes'])) {
                $map['notes'] = $index;
            }
        }

        // Must at least identify 'name' to qualify as a valid header row
        return isset($map['name']) ? $map : [];
    }

    /**
     * Fallback positional mapping if headers are not recognized.
     *
     * @return array<string, int>
     */
    private function fallbackPositionalMap(): array
    {
        return [
            'name' => 0,
            'company' => 1,
            'pic_name' => 2,
            'whatsapp' => 3,
            'email' => 4,
            'location' => 5,
            'service' => 6,
            'category' => 7,
            'service_number' => 8,
            'subscription_price' => 9,
            'notes' => 10,
        ];
    }

    /**
     * Map a raw spreadsheet row into customer data fields.
     */
    private function mapRowToCustomerData(array $row, array $headerMap, string $defaultCategory): array
    {
        $get = function (string $key) use ($row, $headerMap): ?string {
            if (! isset($headerMap[$key])) {
                return null;
            }
            $colIndex = $headerMap[$key];
            $val = $row[$colIndex] ?? '';
            $trimmed = trim((string) $val);

            return $trimmed !== '' ? $trimmed : null;
        };

        $priceVal = $get('subscription_price');
        $price = null;
        if ($priceVal !== null) {
            $cleanPrice = preg_replace('/[^\d.]/', '', str_replace(',', '.', $priceVal));
            if (is_numeric($cleanPrice)) {
                $price = (float) $cleanPrice;
            }
        }

        $categoryRaw = $get('category');
        $category = $defaultCategory;
        if ($categoryRaw) {
            $upper = strtoupper($categoryRaw);
            if (in_array($upper, Customer::CATEGORIES, true)) {
                $category = $upper;
            }
        }

        return [
            'name' => $get('name'),
            'category' => $category,
            'company' => $get('company'),
            'pic_name' => $get('pic_name'),
            'whatsapp' => $get('whatsapp'),
            'email' => $get('email'),
            'location' => $get('location'),
            'service' => $get('service'),
            'service_number' => $get('service_number'),
            'subscription_price' => $price,
            'notes' => $get('notes'),
            'is_active' => true,
            'activation_status' => 'ACTIVE',
        ];
    }

    /**
     * Sanitize and normalize phone number for Indonesian mobile format.
     */
    private function sanitizePhone(string $phone): string
    {
        // Remove spaces, hyphens, parentheses, etc.
        $clean = preg_replace('/[^\d+]/', '', $phone);

        // Remove leading '+'
        if (str_starts_with($clean, '+')) {
            $clean = substr($clean, 1);
        }

        // If starts with 8, prefix with 0
        if (str_starts_with($clean, '8')) {
            $clean = '0'.$clean;
        }

        return $clean;
    }

    /**
     * Generate an Excel (.xlsx) template with sample data and instructions.
     */
    public function generateXlsxTemplate(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Pelanggan');

        // Headers
        $headers = [
            'A1' => 'Nama Pelanggan *',
            'B1' => 'Nama Perusahaan',
            'C1' => 'Nama PIC',
            'D1' => 'No WhatsApp',
            'E1' => 'Email',
            'F1' => 'Lokasi / Alamat',
            'G1' => 'Layanan',
            'H1' => 'Kategori (HOME/CORPORATE/RESELLER)',
            'I1' => 'Nomor Layanan (Kosongkan = Otomatis)',
            'J1' => 'Harga Langganan (Opsional)',
            'K1' => 'Catatan',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $headerRange = 'A1:K1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0B1D3A');
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Sample Data Rows
        $samples = [
            [
                'PT Digital Mitra Sejahtera',
                'PT Digital Mitra Sejahtera',
                'Budi Santoso',
                '081234567890',
                'budi@mitradigital.co.id',
                'Gedung Cyber 2 Lt. 12, Jl. HR Rasuna Said, Jakarta',
                'Dedicated Internet 100 Mbps',
                'CORPORATE',
                '', // Biarkan kosong agar terisi otomatis 920220000X
                '2500000',
                'Koneksi FO Dedicated 1:1',
            ],
            [
                'Ahmad Fauzi',
                '',
                'Ahmad Fauzi',
                '085712345678',
                'fauzi.ahmad@gmail.com',
                'Perum Graha Indah Blok C3 No. 15, Tangerang',
                'Broadband Home 50 Mbps',
                'HOME',
                '', // Biarkan kosong agar terisi otomatis 920230000X
                '350000',
                'Pemasangan router Wi-Fi 6',
            ],
            [
                'CV Berkah Multi Network',
                'CV Berkah Multi Network',
                'Rina Wijaya',
                '081398765432',
                'noc@berkahnet.id',
                'Jl. Raya Bogor KM 30 No. 88, Depok',
                'IP Transit 1 Gbps',
                'RESELLER',
                '', // Biarkan kosong agar terisi otomatis 920210000X
                '8000000',
                'Mitra Reseller Area Depok',
            ],
        ];

        $rowIdx = 2;
        foreach ($samples as $sample) {
            foreach ($sample as $colIdx => $val) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
                // Explicitly format phone number as string
                if ($colIdx === 3) {
                    $sheet->setCellValueExplicit($colLetter.$rowIdx, (string) $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($colLetter.$rowIdx, $val);
                }
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(22);
            $rowIdx++;
        }

        // Apply borders and zebra styling to sample rows
        $dataRange = 'A2:K4';
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D0D7DE');
        $sheet->getStyle('A1:K4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Auto-fit column widths
        foreach (range(1, 11) as $col) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Generate CSV content with UTF-8 BOM for Indonesian Excel compatibility.
     */
    public function generateCsvContent(): string
    {
        $headers = [
            'Nama Pelanggan *',
            'Nama Perusahaan',
            'Nama PIC',
            'No WhatsApp',
            'Email',
            'Lokasi / Alamat',
            'Layanan',
            'Kategori (HOME/CORPORATE/RESELLER)',
            'Nomor Layanan (Kosongkan = Otomatis)',
            'Harga Langganan (Opsional)',
            'Catatan',
        ];

        $samples = [
            [
                'PT Digital Mitra Sejahtera',
                'PT Digital Mitra Sejahtera',
                'Budi Santoso',
                "'081234567890",
                'budi@mitradigital.co.id',
                'Gedung Cyber 2 Lt. 12, Jl. HR Rasuna Said, Jakarta',
                'Dedicated Internet 100 Mbps',
                'CORPORATE',
                '',
                '2500000',
                'Koneksi FO Dedicated 1:1',
            ],
            [
                'Ahmad Fauzi',
                '',
                'Ahmad Fauzi',
                "'085712345678",
                'fauzi.ahmad@gmail.com',
                'Perum Graha Indah Blok C3 No. 15, Tangerang',
                'Broadband Home 50 Mbps',
                'HOME',
                '',
                '350000',
                'Pemasangan router Wi-Fi 6',
            ],
            [
                'CV Berkah Multi Network',
                'CV Berkah Multi Network',
                'Rina Wijaya',
                "'081398765432",
                'noc@berkahnet.id',
                'Jl. Raya Bogor KM 30 No. 88, Depok',
                'IP Transit 1 Gbps',
                'RESELLER',
                '',
                '8000000',
                'Mitra Reseller Area Depok',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM for Microsoft Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers);
        foreach ($samples as $sample) {
            fputcsv($output, $sample);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content ?: '';
    }
}
