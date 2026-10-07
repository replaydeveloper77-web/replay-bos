<?php

namespace Database\Seeders;

use App\Models\WhatsAppTemplate;
use Illuminate\Database\Seeder;

class WhatsAppTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'INCIDENT',
                'name' => 'Incident (Gangguan Baru)',
                'body' => <<<'TEXT'
PT REPLAY INTI MEDIA

Yth. Bapak/Ibu {{nama}},

Kami informasikan bahwa saat ini terdapat gangguan pada layanan:

Layanan:
{{layanan}}

Lokasi:
{{lokasi}}

Ticket:
{{ticket_id}}

Status:
{{status}}

Waktu mulai:
{{waktu_mulai}}

Tim NOC PT Replay Inti Media sedang melakukan penanganan.

Update berikutnya akan kami informasikan melalui WhatsApp.

Terima kasih atas pengertiannya.

— NOC PT Replay Inti Media
TEXT,
            ],
            [
                'key' => 'INVESTIGATING',
                'name' => 'Investigating (Sedang Diperiksa)',
                'body' => <<<'TEXT'
PT REPLAY INTI MEDIA

Yth. Bapak/Ibu {{nama}},

Tim NOC kami saat ini sedang melakukan investigasi pada layanan:

Layanan:
{{layanan}}

Ticket:
{{ticket_id}}

Status:
{{status}}

Kami akan segera menginformasikan perkembangan penanganan.

Terima kasih atas kesabarannya.

— NOC PT Replay Inti Media
TEXT,
            ],
            [
                'key' => 'MAINTENANCE',
                'name' => 'Maintenance (Pemeliharaan)',
                'body' => <<<'TEXT'
PT REPLAY INTI MEDIA

Yth. Bapak/Ibu {{nama}},

Kami informasikan akan/sedang dilakukan pemeliharaan pada layanan:

Layanan:
{{layanan}}

Lokasi:
{{lokasi}}

Ticket:
{{ticket_id}}

Mohon maaf atas ketidaknyamanannya selama proses pemeliharaan berlangsung.

— NOC PT Replay Inti Media
TEXT,
            ],
            [
                'key' => 'MONITORING',
                'name' => 'Monitoring (Pemantauan)',
                'body' => <<<'TEXT'
PT REPLAY INTI MEDIA

Yth. Bapak/Ibu {{nama}},

Layanan Anda sudah kembali normal dan saat ini dalam tahap pemantauan tim NOC kami:

Layanan:
{{layanan}}

Ticket:
{{ticket_id}}

Apabila kendala kembali terjadi, mohon segera hubungi Tim NOC PT Replay Inti Media.

Terima kasih.

— NOC PT Replay Inti Media
TEXT,
            ],
            [
                'key' => 'RESOLVED',
                'name' => 'Resolved (Selesai)',
                'body' => <<<'TEXT'
PT REPLAY INTI MEDIA

Yth. Bapak/Ibu {{nama}},

Gangguan pada layanan:

{{layanan}}

Ticket:
{{ticket_id}}

telah selesai ditangani.

Waktu mulai:
{{waktu_mulai}}

Waktu pemulihan:
{{waktu_resolved}}

Status:
RESOLVED

Mohon melakukan pengecekan kembali layanan.

Apabila masih terdapat kendala, silakan menghubungi NOC PT Replay Inti Media.

Terima kasih.
TEXT,
            ],
            [
                'key' => 'CLOSED',
                'name' => 'Closed (Ticket Ditutup)',
                'body' => <<<'TEXT'
PT REPLAY INTI MEDIA

Yth. Bapak/Ibu {{nama}},

Ticket layanan Anda telah kami tutup:

Layanan:
{{layanan}}

Ticket:
{{ticket_id}}

Status:
CLOSED

Terima kasih telah mempercayakan layanan Anda kepada PT Replay Inti Media.

— NOC PT Replay Inti Media
TEXT,
            ],
            [
                'key' => 'CUSTOM',
                'name' => 'Custom (Isi Manual)',
                'body' => '',
            ],
        ];

        foreach ($templates as $template) {
            WhatsAppTemplate::updateOrCreate(['key' => $template['key']], $template);
        }
    }
}
