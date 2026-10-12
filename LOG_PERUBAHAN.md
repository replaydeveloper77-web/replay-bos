# Log Perubahan dan Update Sistem Replay NOC (Replay BOS)

Dokumen ini mencatat seluruh riwayat pembaruan, perbaikan, dan penyesuaian fitur pada aplikasi **Replay NOC** secara berkala dari **Selasa, 06 Oktober 2026** hingga **Senin, 12 Oktober 2026** (Update Terakhir: 12 Oktober 2026).

---

## 📌 Ringkasan Pembaruan (Executive Summary)

| No | Waktu (Hari, Tanggal Bulan Tahun & Jam) | Modul / Fitur | Kategori | Ringkasan Singkat |
|:---|:---|:---|:---|:---|
| 1 | **Selasa, 06 Oktober 2026 — 09:10:56 WIB** | Server Localhost & DB | Setup / Dev | Panduan eksekusi aplikasi di localhost (MariaDB, `.env`, migrasi, artisan serve). |
| 2 | **Selasa, 06 Oktober 2026 — 09:54:10 WIB** | Autentikasi / 2FA | Security / Config | Penonaktifan sementara 2FA agar mempermudah login tim di localhost. |
| 3 | **Selasa, 06 Oktober 2026 — 10:13:39 WIB** | Manajemen Tiket | Feature | Penambahan status `HANDLE`, filter kategori, dan statistik bar Corporate, Reseller, Home. |
| 4 | **Selasa, 06 Oktober 2026 — 10:38:51 WIB** | Kategori Gangguan & Master Client | Enhancement | Penambahan 8 kategori gangguan baru dan integrasi otomatisasi judul tiket dari nama client. |
| 5 | **Selasa, 06 Oktober 2026 — 11:14:10 WIB** | Tabel Tiket & Filter Tanggal | UI/UX & Feature | Pembersihan label ganda di bawah nomor tiket, perapihan urutan kolom, dan filter tanggal/bulan/tahun. |
| 6 | **Selasa, 06 Oktober 2026 — 11:52:49 WIB** | Form Pembuatan Tiket | UX Optimization | Penyederhanaan form buat tiket agar tidak input manual ganda (nama & WhatsApp dari master data). |
| 7 | **Selasa, 06 Oktober 2026 — 12:18:37 WIB** | Pagination Tiket | Performance & UI | Pembatasan list tiket 10 per halaman dengan pagination Bootstrap (Next/Prev). |
| 8 | **Selasa, 06 Oktober 2026 — 12:28:56 WIB** | Format Nomor Tiket | Standardization | Perubahan prefix nomor tiket Home dari `PLG-` menjadi `RH-` untuk standarisasi format 2 huruf. |
| 9 | **Selasa, 06 Oktober 2026 — 13:34:52 WIB** | Statistik & Hapus Tiket | Feature & UI | Penambahan counter statistik `Handle` & `Closed` serta tombol hapus tiket (icon trash merah). |
| 10 | **Selasa, 06 Oktober 2026 — 13:57:11 WIB** | Frontend Stack Integration | Architecture | Integrasi Tailwind CSS v4, Vue 3, komponen `TicketBadge.vue` dan `TicketStatsCard.vue`. |
| 11 | **Selasa, 06 Oktober 2026 — 14:27:30 WIB** | Sistem Notifikasi Flash | Bugfix | Perbaikan notifikasi ganda (*double alert*) saat create dan delete tiket. |
| 12 | **Selasa, 06 Oktober 2026 — 14:52:45 WIB** | Data Pelanggan | Feature | Penambahan tombol hapus pelanggan (icon trash merah) dengan konfirmasi keamanan. |
| 13 | **Rabu, 07 Oktober 2026 — 09:44:11 WIB** | Template WhatsApp Detail Tiket | Critical Bugfix | Perbaikan menyeluruh fitur auto-fill template WA: isolasi Vue 3 `#app`, re-seed DB, dan multi-layer event listener. |
| 14 | **Kamis, 08 Oktober 2026 — 10:05:00 WIB** | Data Pelanggan / Import Spreadsheet | Feature | Fitur input data pelanggan cepat via spreadsheet (Excel .xlsx/.xls & CSV) dengan auto-generate nomor layanan (PLG-, CP-, RS-). |
| 15 | **Kamis, 08 Oktober 2026 — 10:25:00 WIB** | Timeline & Live Tracking Client | Feature & UI/UX | Progress update perbaikan dengan foto di timeline dan tampilan link publik client berbasis mockup handphone. |
| 16 | **Kamis, 08 Oktober 2026 — 11:15:00 WIB** | Link Tracking Permanen & Laporan RFO | Feature & UI/UX | Link tracking tidak kadaluarsa, tiket solved/closed tetap dapat dipantau beserta sajian lengkap laporan RFO (Reason For Outage). |
| 17 | **Kamis, 08 Oktober 2026 — 11:45:00 WIB** | Anti-Overflow & Word-Wrap Sharelink | UI/UX Bugfix | Perbaikan teks offside keluar kolom pada card timeline mockup HP dengan rule strict overflow-wrap & eliminasi horizontal scrollbar. |
| 18 | **Kamis, 08 Oktober 2026 — 12:15:00 WIB** | Perbaikan Text Alignment Timeline RFO | UI/UX Bugfix | Perbaikan bug pergeseran teks ke kanan akibat preservasi spasi indentasi Blade pada white-space: pre-wrap, pemisahan header RFO, dan text-align left presisi. |
| 19 | **Kamis, 08 Oktober 2026 — 12:30:00 WIB** | Setup Akses Publik / Ngrok Tunnel | DevOps / Testing | Konfigurasi Trust Proxies Laravel & aktivasi tunnel ngrok untuk pengujian sementara fitur tiket & sharelink oleh karyawan via internet/HP. |
| 20 | **Jumat, 09 Oktober 2026 — 09:40:00 WIB** | Standarisasi Nomor Layanan Client | Standardization | Pembaruan format nomor layanan pelanggan menjadi angka murni: `920210000[urut]` (Reseller), `920220000[urut]` (Corporate), dan `920230000[urut]` (Home). |
| 21 | **Jumat, 09 Oktober 2026 — 10:35:00 WIB** | Portal Baru Teknisi & Registrasi Role | Feature & Role | Penambahan Portal Teknisi Lapangan (`/portal-teknisi`), menu sidebar, permission `teknisi.portal.view`, serta role baru `Teknisi` di halaman Roles & Permissions yang siap dikelola mandiri. |
| 22 | **Jumat, 09 Oktober 2026 — 11:15:00 WIB** | Perbaikan Scrollbar & Responsivitas Sidebar | UI/UX & Layout | Pemasangan custom sleek scrollbar vertikal 100vh terisolasi dan header brand sticky agar menu bawah mudah diakses. |
| 23 | **Jumat, 09 Oktober 2026 — 11:22:00 WIB** | Fitur Tutup / Kembalikan Sidebar Mobile | Mobile UX | Eliminasi tombol X sesuai revisi; penutupan sidebar mobile menggunakan backdrop overlay gelap (klik area luar), shortcut Escape, dan auto-close navigasi. |
| 24 | **Jumat, 09 Oktober 2026 — 11:40:00 WIB** | Menu Upgrade/Downgrade & Persetujuan Finance | Feature & Automation | Penambahan modul Upgrade/Downgrade paket, formulir pengajuan, approval Finance, update paket customer, dan auto-generate nomor Work Order Upgrade otomatis. |
| 25 | **Jumat, 09 Oktober 2026 — 12:45:00 WIB** | Penyempurnaan Form Upgrade/Downgrade | UI/UX & Enhancement | Pemilihan client difilter berdasarkan kategori (Reseller, Corporate, Home), pengetikan bebas paket baru tujuan dengan saran autocomplete, serta penghapusan input biaya langganan baru dari form pengajuan. |
| 26 | **Jumat, 09 Oktober 2026 — 13:30:00 WIB** | Menu Resource Database Monitoring (cPanel) | Feature & DevOps | Penambahan menu monitoring database resource cPanel (`/resources`), visualisasi metrik ukuran DB, rincian tabel, koneksi aktif, disk hosting, performa PHP/MySQL, serta aksi optimasi tabel database. |
| 27 | **Jumat, 09 Oktober 2026 — 14:25:00 WIB** | Penyelarasan Nomor Layanan Reseller & Command Tool | Data Fix & CLI | Penyelarasan otomatis 64 nomor layanan Reseller yang sempat loncat (9202100005) akibat perpindahan ke Corporate, serta pembuatan perintah artisan `customers:align-service-numbers`. |
| 28 | **Jumat, 09 Oktober 2026 — 14:45:00 WIB** | Readonly & Format Titik Harga Satuan Penawaran | UI/UX & Security | Penguncian input harga satuan di menu penawaran menjadi readonly otomatis dari paket layanan serta pemformatan ribuan dengan titik (contoh: 3.000.000). |
| 29 | **Jumat, 09 Oktober 2026 — 15:45:00 WIB** | Standardisasi Label BPJS Ketenagakerjaan & BPJS Kesehatan | UI/UX Refinement | Penyesuaian nama label "Nomor JMO (BPJS TK)" menjadi "Nomor BPJS Ketenagakerjaan" dan "Nomor KPJ (Jamsostek)" menjadi "Nomor BPJS Kesehatan" di seluruh modul Karyawan & Portal. |
| 30 | **Jumat, 09 Oktober 2026 — 16:45:00 WIB** | Detail Absensi: Foto Live, Watermark Logo & Timestamp Replay, Alamat & GPS | Feature & Security | Penambahan sistem absensi mandiri lengkap: foto selfie langsung (live camera) dengan watermark resmi Replay BOS (logo Replay, waktu WIB detik real-time, badge check-in/out, nama & NIK karyawan, koordinat GPS, dan alamat), tersimpan di DB & storage, preview thumbnail, dan modal lightbox. |
| 31 | **Jumat, 09 Oktober 2026 — 17:05:00 WIB** | Portal Karyawan / Lightbox Foto Absensi | Frontend Bugfix | Perbaikan error `TypeError: w.value.lat.toFixed is not a function` saat membuka foto absensi di Portal Karyawan melalui fungsi `formatCoordinate()` yang aman untuk tipe data string maupun number dari Eloquent cast. |
| 32 | **Minggu, 11 Oktober 2026 — 01:25:00 WIB** | Redesign Modal Absensi Check-in & Standardisasi Watermark Resmi Replay | UI/UX & Security | Redesign modal absensi menjadi clean enterprise minimalis, live camera indicator, eliminasi total fitur upload file/galeri lokal, serta standardisasi watermark resmi Replay (logo, PT. Replay Inti Media, timestamp WIB, alamat, GPS). |
| 33 | **Minggu, 11 Oktober 2026 — 23:36:00 WIB** | Deployment Hosting / Vendor Package | DevOps / Deployment | Pembuatan arsip `vendor.zip` (43 MB) di root project untuk memfasilitasi migrasi & deployment langsung ke cPanel hosting tanpa Composer CLI. |
| 34 | **Minggu, 11 Oktober 2026 — 23:54:00 WIB** | Symlink Storage & Fallback Foto cPanel | Infrastructure & Bugfix | Peningkatan endpoint `/storage-link` (auto-create subfolder, permission check, feedback HTML informatif) dan penambahan route fallback `/storage/{path}` agar foto absensi & profil karyawan tetap tampil 100% pada hosting yang membatasi symlink. |
| 35 | **Senin, 12 Oktober 2026 — 00:02:00 WIB** | Konfigurasi Filesystem Storage | Config / Security | Penonaktifan opsi bawaan `'serve' => false` pada disk local dan public (`config/filesystems.php`) untuk mencegah konflik routing asset storage dan mengoptimalkan kompatibilitas server cPanel. |

---

## 📅 Rincian Log Perubahan

### 1. Selasa, 06 Oktober 2026

#### ⏰ Pukul 09:10:56 WIB — Panduan & Setup Server Localhost
- **Latar Belakang & Kebutuhan:** Setup environment local development agar project Replay BOS NOC dapat berjalan lancar di mesin lokal.
- **Tindakan Teknis:**
  - Konfigurasi file environment [`.env`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/.env) untuk koneksi database MariaDB lokal (`DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=replay_bos`).
  - Eksekusi migrasi tabel dan seeding data awal akun superadmin.
  - Setup instruksi menjalankan service backend menggunakan `php artisan serve` pada port 8000 dan static assets.

---

#### ⏰ Pukul 09:54:10 WIB — Penonaktifan Sementara Two-Factor Authentication (2FA)
- **Latar Belakang & Kebutuhan:** Di lingkungan localhost, pengiriman kode OTP/2FA via email atau aplikasi authenticator dapat menghambat proses testing cepat tim NOC.
- **Tindakan Teknis:**
  - Penyesuaian konfigurasi autentikasi login pengguna di localhost untuk membypass pengecekan wajib 2FA, sehingga login dapat langsung menggunakan username dan password terdaftar.

---

#### ⏰ Pukul 10:13:39 WIB — Penambahan Status HANDLE & Stat Bar Kategori Tiket
- **Latar Belakang & Kebutuhan:** Kebutuhan status khusus untuk menandai tiket yang sedang aktif ditangani teknisi, serta tampilan cepat jumlah tiket berdasarkan kategori pelanggan.
- **File Terkait:**
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
  - [`resources/views/tickets/index.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/index.blade.php)
  - [`resources/views/tickets/show.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/show.blade.php)
- **Tindakan Teknis:**
  - Menambahkan nilai `HANDLE` pada enum/alur status tiket (label UI: *"HANDLE (Sedang Ditangani)"*).
  - Menyediakan filter query pencarian tiket berdasarkan kategori (`CORPORATE`, `RESELLER`, `HOME`).
  - Merancang komponen stat card di bagian atas halaman index tiket yang menghitung dan menampilkan jumlah total tiket Corporate (`CP-`), Reseller (`RS-`), dan Home (`RH-`).

---

#### ⏰ Pukul 10:38:51 WIB — Pembaruan 8 Kategori Gangguan & Efisiensi Judul Tiket
- **Latar Belakang & Kebutuhan:** Standarisasi jenis gangguan jaringan dan eliminasi duplikasi input judul tiket secara manual saat input tiket gangguan.
- **File Terkait:**
  - [`resources/views/tickets/_form.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/_form.blade.php)
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
- **Tindakan Teknis:**
  - Menambahkan 8 kategori layanan gangguan:
    1. `Link Down`
    2. `Gangguan Bandwidth`
    3. `High Latency / Intermitten`
    4. `Aktivasi`
    5. `Dismantle`
    6. `Upgrade`
    7. `Downgrade`
    8. `Non Gangguan`
  - Menyederhanakan alur pembuatan tiket: petugas cukup memilih nama client yang sudah terdaftar di master data, dan judul tiket dibuat secara otomatis berbasis jenis gangguan dan nama pelanggan.

---

#### ⏰ Pukul 11:14:10 WIB — Perapihan Tabel Tiket & Penambahan Filter Periode Tanggal
- **Latar Belakang & Kebutuhan:** Tampilan tabel sebelumnya memiliki label ganda jenis tiket di bawah nomor tiket. Selain itu, diperlukan filter data berdasarkan tanggal, bulan, dan tahun.
- **File Terkait:**
  - [`resources/views/tickets/index.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/index.blade.php)
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
- **Tindakan Teknis:**
  - Menghapus label redundant di bawah nomor tiket karena inisial kode prefix sudah jelas (`CP`, `RS`, `RH`).
  - Menstandarkan urutan kolom tabel menjadi: **Nomor Tiket**, **Nama Tiket / Perusahaan**, **Category**, **Priority**, **Status**, **Waktu SLA**, dan **Tiket Dibuat**.
  - Mengimplementasikan filter form berbasis tanggal spesifik (`filter_date`), bulan (`filter_month`), dan tahun (`filter_year`) pada query builder tiket.

---

#### ⏰ Pukul 11:52:49 WIB — Optimalisasi Form Pembuatan Tiket
- **Latar Belakang & Kebutuhan:** Menghilangkan pemborosan waktu dan data ganda dengan tidak meminta input manual nama pelanggan dan nomor WhatsApp, melainkan langsung mengambilnya dari relasi master pelanggan.
- **File Terkait:**
  - [`resources/views/tickets/create.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/create.blade.php)
  - [`resources/views/tickets/_form.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/_form.blade.php)
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
- **Tindakan Teknis:**
  - Membatasi field input form buat tiket hanya pada: **Pilih Client**, **Tipe Pelanggan**, **Category**, **Priority**, **Handle By (NOC / Vendor / dll)**, dan **Deskripsi Gangguan**.
  - Logika penyimpanan otomatis memetakan nomor telepon dan nama kontak langsung dari data pelanggan terpilih.

---

#### ⏰ Pukul 12:18:37 WIB — Pembatasan Pagination 10 Tiket per Halaman
- **Latar Belakang & Kebutuhan:** Mencegah overload data pada tabel tiket dan mempermudah navigasi list tiket.
- **File Terkait:**
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
  - [`resources/views/tickets/index.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/index.blade.php)
- **Tindakan Teknis:**
  - Mengubah pengambilan data dari `->get()` menjadi `->paginate(10)->withQueryString()`.
  - Menambahkan elemen pagination navigasi (`{{ $tickets->links() }}`) dengan styling Bootstrap 5.

---

#### ⏰ Pukul 12:28:56 WIB — Penyeragaman Format Nomor Tiket Home ke `RH-`
- **Latar Belakang & Kebutuhan:** Penyeragaman kode tiket gangguan perumahan/home agar konsisten menggunakan format 2 huruf kapital diikuti angka tahun/bulan/hari/nomor urut.
- **File Terkait:**
  - [`app/Models/Ticket.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Models/Ticket.php)
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
- **Tindakan Teknis:**
  - Mengganti kode format tiket dari `PLG-` menjadi `RH-` (Replay Home):
    - `CP-YYYYMMDD-XXXX` : Corporate
    - `RS-YYYYMMDD-XXXX` : Reseller
    - `RH-YYYYMMDD-XXXX` : Home

---

#### ⏰ Pukul 13:34:52 WIB — Counter Tiket Handle/Closed & Tombol Hapus Tiket
- **Latar Belakang & Kebutuhan:** Memonitor tiket yang sedang di-handle dan yang sudah selesai (closed), serta menambahkan opsi penghapusan tiket langsung dari tabel baris data.
- **File Terkait:**
  - [`resources/views/tickets/index.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/index.blade.php)
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
  - [`app/Policies/TicketPolicy.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Policies/TicketPolicy.php)
- **Tindakan Teknis:**
  - Menambahkan card metrik tiket berstatus `HANDLE` dan `CLOSED` di atas list tiket.
  - Menambahkan method `destroy` pada `TicketController` dengan otorisasi policy `delete`.
  - Menambahkan tombol form hapus dengan ikon trash merah (`bi bi-trash text-danger`) dan konfirmasi `onclick="return confirm(...)"` di setiap baris aksi tiket.

---

#### ⏰ Pukul 13:57:11 WIB — Integrasi Vue 3 dan Tailwind CSS
- **Latar Belakang & Kebutuhan:** Mengadopsi teknologi Vue.js dan Tailwind CSS dalam ekosistem Laravel Blade yang sudah ada.
- **File Terkait:**
  - [`vite.config.js`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/vite.config.js)
  - [`resources/js/app.js`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/js/app.js)
  - [`resources/css/app.css`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/css/app.css)
  - [`resources/js/components/TicketBadge.vue`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/js/components/TicketBadge.vue)
  - [`resources/js/components/TicketStatsCard.vue`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/js/components/TicketStatsCard.vue)
- **Tindakan Teknis:**
  - Instalasi dan konfigurasi plugin `@tailwindcss/vite` serta `@vitejs/plugin-vue`.
  - Membuat komponen Vue modern dengan utility styling Tailwind CSS (`TicketBadge` indikator aktif dan `TicketStatsCard`).
  - Menjalankan proses build aset melalui Vite.

---

#### ⏰ Pukul 14:27:30 WIB — Perbaikan Notifikasi Ganda (Double Alert)
- **Latar Belakang & Kebutuhan:** Saat tiket berhasil dibuat atau dihapus, notifikasi pesan status muncul 2 kali bertumpuk di layar.
- **File Terkait:**
  - [`resources/views/layouts/app.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/layouts/app.blade.php)
  - [`resources/views/tickets/index.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/index.blade.php)
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
- **Tindakan Teknis:**
  - Menghilangkan duplikasi blok render `@if (session('status'))` antara layout master dan view anak.
  - Menyelaraskan session key pesan feedback sehingga setiap aksi hanya menampilkan satu notifikasi alert Bootstrap yang bersih dan dapat di-*dismiss*.

---

#### ⏰ Pukul 14:52:45 WIB — Penambahan Fitur Hapus Pelanggan di Master Data
- **Latar Belakang & Kebutuhan:** Menyamakan fungsionalitas manajemen data pada menu Data Pelanggan agar memiliki tombol hapus seperti pada menu tiket.
- **File Terkait:**
  - [`resources/views/customers/index.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/customers/index.blade.php)
  - [`app/Http/Controllers/CustomerController.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Http/Controllers/CustomerController.php)
  - [`app/Policies/CustomerPolicy.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/app/Policies/CustomerPolicy.php)
- **Tindakan Teknis:**
  - Menambahkan route `DELETE /customers/{customer}` (`customers.destroy`).
  - Mengimplementasikan pengecekan relasi pelanggan terhadap tiket gangguan yang aktif untuk menjaga integritas data sebelum penghapusan.
  - Menambahkan tombol aksi delete berikon `<i class="bi bi-trash"></i>` warna merah dengan konfirmasi alert.

---

### 2. Rabu, 07 Oktober 2026

#### ⏰ Pukul 09:44:11 WIB — Perbaikan Fitur Template Pesan WhatsApp di Detail Tiket (Solusi Tuntas)
- **Latar Belakang & Kebutuhan:** Pada halaman detail tiket (`/tickets/{ticket}`), ketika pengguna memilih opsi pada dropdown *"Template"* di kartu *"Kirim WhatsApp (Template)"*, teks template tidak otomatis muncul di textarea pesan.
- **File Terkait:**
  - [`resources/views/layouts/app.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/layouts/app.blade.php)
  - [`resources/views/tickets/index.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/index.blade.php)
  - [`resources/views/tickets/show.blade.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/resources/views/tickets/show.blade.php)
  - [`database/seeders/WhatsAppTemplateSeeder.php`](file:///home/prtmagum/Documents/replay-bos-prod/replay-noc/database/seeders/WhatsAppTemplateSeeder.php)
- **Akar Masalah (Root Cause):**
  1. **Interferensi Vue 3 Global Mount:** Di `layouts/app.blade.php`, kontainer pembungkus halaman diberikan `id="app"`. Saat script Vite dieksekusi, Vue me-mount seluruh halaman dan merekonstruksi DOM menjadi Virtual DOM. Hal ini menyebabkan event listener bawaan JavaScript (seperti `addEventListener('change')` pada dropdown template) terputus dan hilang.
  2. **Data Template Database Corrupt:** Baris data template WhatsApp di database sempat berisi teks rusak/help dump akibat eksekusi command sebelumnya.
  3. **Penanganan Event Browser Cache & Opsi Kosong:** Script sebelumnya tidak menangani *browser form restoration* saat reload dan tidak mereset textarea jika user memilih opsi manual/kosong.
- **Tindakan & Solusi Teknis:**
  1. **Restorasi Database:** Menjalankan seeder `php artisan db:seed --class=WhatsAppTemplateSeeder` untuk memastikan semua template (`INCIDENT`, `INVESTIGATING`, `MAINTENANCE`, `MONITORING`, `RESOLVED`, `CLOSED`, `CUSTOM`) memiliki teks dan placeholder yang valid.
  2. **Isolasi Scope Mount Vue 3:**
     - Mengubah container utama di `layouts/app.blade.php` menjadi `<div class="noc-content">` (tanpa `id="app"`).
     - Memindahkan `id="app"` khusus hanya membungkus komponen Vue di `tickets/index.blade.php`:
       ```html
       <div id="app" class="d-inline-flex">
           <ticket-badge></ticket-badge>
       </div>
       ```
     - Hasilnya, halaman detail tiket dan halaman Blade lainnya bebas dari interferensi DOM clobbering Vue 3.
  3. **Penguatan Logika Render Template (`tickets/show.blade.php`):**
     - Menambahkan handler inline `onchange="window.renderWaTemplate && window.renderWaTemplate()"` pada tag `<select id="wa-template-select">`.
     - Mengekspos fungsi render secara global `window.renderWaTemplate = renderTemplate;`.
     - Menambahkan delegasi event pada `document.addEventListener('change', ...)` sebagai pengaman bertingkat (*multi-layer event listener*).
     - Menambahkan auto-render saat halaman dimuat jika template sudah terpilih (*browser cache handling*).
     - Mendukung update real-time jika nama penerima kustom diubah pada pilihan penerima kustom.
     - Mengosongkan textarea secara otomatis ketika memilih opsi *"-- Tanpa template (tulis manual) --"* atau *"Custom (Isi Manual)"*.
- **Hasil Pengujian (Testing):**
  - Diuji secara otomatis melalui Chrome DevTools Protocol:
    - Pemilihan `INCIDENT`, `MAINTENANCE`, `RESOLVED`, `CLOSED` langsung memunculkan teks dengan placeholder `{{nama}}`, `{{layanan}}`, `{{lokasi}}`, `{{ticket_id}}`, `{{status}}`, `{{waktu_mulai}}`, dan `{{waktu_resolved}}` yang tersubstitusi sempurna.
    - Pemilihan opsi kosong langsung mengosongkan textarea untuk input manual.
    - Komponen `<ticket-badge>` pada halaman list tiket tetap aktif normal.

---

### 3. Kamis, 08 Oktober 2026

#### ⏰ Pukul 10:05:00 WIB — Fitur Import Spreadsheet & Penomoran Layanan Otomatis
- **Latar Belakang & Kebutuhan:** Tim NOC dan Marketing membutuhkan cara cepat untuk menginput banyak data client/pelanggan sekaligus menggunakan file spreadsheet (Excel `.xlsx`/`.xls` dan `.csv`). Sistem diminta memetakan kolom data (nama pelanggan, nama perusahaan, nama PIC, no whatsapp, email, lokasi/alamat, layanan) dan membuat nomor layanan unik secara otomatis (`PLG-XXXXX` untuk Home, `CP-XXXXX` untuk Corporate, dan `RS-XXXXX` untuk Reseller).
- **File Terkait:**
  - [`app/Services/CustomerImportService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/CustomerImportService.php) (Baru)
  - [`app/Http/Requests/ImportCustomerRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Requests/ImportCustomerRequest.php) (Baru)
  - [`app/Http/Controllers/CustomerController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/CustomerController.php)
  - [`routes/web.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/routes/web.php)
  - [`resources/views/customers/index.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/customers/index.blade.php)
  - [`tests/Feature/CustomerImportTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/CustomerImportTest.php) (Baru)
- **Tindakan Teknis:**
  1. **Instalasi PhpSpreadsheet:** Menginstal library `phpoffice/phpspreadsheet` v5.10 via Composer untuk parsing spreadsheet performa tinggi (.xlsx, .xls, .csv).
  2. **Service Parser & Smart Header Normalization (`CustomerImportService`):**
     - Mendukung variasi penulisan header fleksibel (misal: *Nama Pelanggan / Nama / Client*, *No WhatsApp / WhatsApp / No WA / No Telepon*, *Lokasi/Alamat / Alamat*, dll).
     - Otomatis menormalisasi nomor telepon (membersihkan karakter non-digit, mengonversi angka Excel dengan format standar `08...` / `62...`).
     - Menyediakan method pembuatan template Excel (`generateXlsxTemplate`) dengan styling header rapi, auto-width, dan contoh data baris.
     - Menyediakan method pembuatan template CSV (`generateCsvContent`) dengan UTF-8 BOM untuk kompatibilitas Microsoft Excel.
  3. **Penomoran Layanan Otomatis:**
     - Mengekspos method `generateServiceNumber($category)` menjadi public di [`CustomerService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/CustomerService.php).
     - Baris yang tidak memiliki nomor layanan otomatis digenerate berurutan dengan prefix yang tepat (`PLG-` untuk Home, `CP-` untuk Corporate, `RS-` untuk Reseller).
  4. **Antarmuka Pengguna (UI/UX Modal Import):**
     - Menambahkan tombol *"Import Spreadsheet"* di samping tombol *"Tambah Pelanggan"*.
     - Modal interaktif lengkap dengan:
       - Tombol langsung unduh Template Excel (.xlsx) & Template CSV (.csv).
       - Input file drag-and-drop dengan filter ekstensi `.xlsx,.xls,.csv`.
       - Pilihan kategori default (`Home`, `Corporate`, `Reseller`).
       - Tabel panduan kolom yang jelas (menjelaskan kolom wajib vs opsional serta nomor layanan otomatis).
       - State submit tombol dengan indikator loading/spinner saat proses import berlangsung.
     - Card ringkasan hasil import di halaman indeks yang menampilkan daftar pelanggan yang baru saja diimport beserta nomor layanan yang berhasil di-generate sistem.
  5. **Pengujian Otomatis (Automated Testing):**
     - Menulis 7 feature test di `CustomerImportTest.php` yang memverifikasi download template, import CSV, import XLSX berbagai kategori, validasi baris kosong/nama wajib, normalisasi nomor telepon, dan pengecekan otorisasi. Seluruh 7 pengujian lulus dengan 43 asersi.

---

#### ⏰ Pukul 10:25:00 WIB — Progress Update Timeline dengan Foto & Tampilan Mockup Handphone untuk Client
- **Latar Belakang & Kebutuhan:** Tim operasional meminta agar fitur timeline pada tiket dapat mencatat tahapan/perkembangan perbaikan secara langsung beserta dokumentasi foto yang melekat pada setiap update perbaikan. Selain itu, link pelacakan tiket yang dibagikan ke client (`/p/{token}`) dirombak agar tidak hanya menampilkan galeri foto saja, melainkan menyajikan riwayat tahapan progres perbaikan lengkap dengan foto dalam antarmuka desain berbingkai **mockup smartphone/handphone** yang interaktif dan responsif.
- **File Terkait:**
  - [`database/migrations/2026_10_22_100001_create_ticket_progress_updates_table.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/database/migrations/2026_10_22_100001_create_ticket_progress_updates_table.php) (Baru)
  - [`app/Models/TicketProgressUpdate.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Models/TicketProgressUpdate.php) (Baru)
  - [`app/Models/TicketAttachment.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Models/TicketAttachment.php)
  - [`app/Models/Ticket.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Models/Ticket.php)
  - [`app/Http/Requests/StoreTicketProgressUpdateRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Requests/StoreTicketProgressUpdateRequest.php) (Baru)
  - [`app/Http/Controllers/TicketController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/TicketController.php)
  - [`app/Http/Controllers/PublicTicketController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/PublicTicketController.php)
  - [`routes/web.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/routes/web.php)
  - [`resources/views/tickets/show.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/tickets/show.blade.php)
  - [`resources/views/public/ticket.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/public/ticket.blade.php)
  - [`tests/Feature/TicketProgressUpdateTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/TicketProgressUpdateTest.php) (Baru)
- **Tindakan Teknis:**
  1. **Tabel Database & Relasi Model:**
     - Membuat tabel `ticket_progress_updates` (`ticket_id`, `user_id`, `title`, `note`, `status`, `created_at`, `updated_at`).
     - Menambahkan foreign key `ticket_progress_update_id` pada tabel `ticket_attachments` sehingga foto dokumentasi dapat terhubung spesifik ke catatan progres perbaikan.
     - Mengintegrasikan relasi `progressUpdates` ke dalam metode `Ticket::timeline()` agar seluruh riwayat penugasan, status, dan progres teknisi tersinkronisasi dalam urutan kronologis.
  2. **Antarmuka Input Progres di Detail Tiket:**
     - Menambahkan tombol *"Update Progress & Foto"* serta form collapsable pada card Timeline di `tickets/show.blade.php`.
     - Teknisi dapat menginput judul kegiatan, catatan perbaikan, opsi perubahan status tiket, serta mengunggah multiple foto dokumentasi sekaligus (maks 5 foto @ 10MB).
     - Menampilkan thumbnail foto terlampir langsung pada balon progres update di timeline, lengkap dengan tombol hapus bagi petugas berwenang.
     - Menambahkan tombol *"Buka Mockup HP"* pada bagian link publik untuk memudahkan petugas mengecek tampilan yang dilihat client.
  3. **Desain Halaman Publik Berbasis Mockup Handphone (`/p/{token}`):**
     - Membangun frame smartphone modern lengkap dengan bezel obsidian, tombol fisik samping, Dynamic Island / punch-hole kamera, status bar (jam live, sinyal 5G, Wi-Fi, baterai), dan home indicator bar.
     - Menyajikan informasi tiket secara lengkap: nomor tiket, status hero banner dengan live pulsing indicator, detail pelanggan & paket, catatan penyelesaian jika selesai, dan tombol bantuan via WhatsApp NOC.
     - Menyajikan timeline vertikal langkah perbaikan teknisi beserta galeri foto dokumentasi di setiap tahapan.
     - Fitur **Lightbox Zoom Modal**: foto dapat diklik untuk diperbesar ke layar penuh dengan informasi waktu pengambilan foto.
     - **Responsivitas Sempurna:** Pada layar desktop tampil sebagai smartphone mockup yang estetis dan mewah; pada perangkat ponsel fisik (lebar layar <= 576px) frame otomatis menyesuaikan menjadi native full-screen yang nyaman dibaca.
  4. **Pengujian Otomatis (Automated Testing):**
     - Menulis 4 pengujian otomatis di `TicketProgressUpdateTest.php` untuk create progress update dengan foto, integrasi timeline, render mockup handphone di halaman publik, dan delete progress update. Seluruh pengujian lulus 100%.

---

#### ⏰ Pukul 11:15:00 WIB — Link Tracking Permanen & Laporan RFO (Reason For Outage)
- **Latar Belakang & Kebutuhan:** Sebelumnya, tautan publik tiket (`/p/{token}`) dibatasi kedaluwarsa (HTTP 410) saat status tiket menjadi `CLOSED`. Klien dan tim membutuhkan tautan yang **tidak pernah kedaluwarsa** agar klien dapat terus memantau proses hingga tuntas, serta melihat riwayat tiket yang sudah **SOLVED/CLOSED** beserta laporan resmi **RFO (Reason For Outage)**.
- **Tindakan Teknis:**
  1. **Tautan Publik Permanen Tanpa Kadaluarsa:**
     - Memperbarui metode `Ticket::isPublicLinkExpired()` untuk mengembalikan `false` secara permanen.
     - Klien yang membuka URL `/p/{token}` pada tiket yang telah berstatus `RESOLVED` maupun `CLOSED` kini tetap dapat mengakses halaman secara penuh (HTTP 200).
     - Menghapus pesan peringatan tautan kedaluwarsa di halaman internal `tickets/show.blade.php`, dan memperjelas label bahwa tautan bersifat permanen dan aman dibagikan sebagai arsip bagi pelanggan.
  2. **Kartu Khusus Laporan RFO (Reason For Outage) di Mockup Handphone:**
     - Menambahkan kartu khusus **Laporan RFO (Reason For Outage)** dengan aksen warna emerald green elegan pada tampilan mockup handphone client (`resources/views/public/ticket.blade.php`).
     - Menyajikan rincian:
       - **Penyebab Gangguan & Solusi Perbaikan (RFO):** Menampilkan catatan resolusi yang diinput oleh teknisi/NOC secara jelas dan rapi.
       - **Durasi Gangguan (Downtime / MTTR):** Dihitung otomatis menggunakan metode baru `Ticket::outageDuration()` (misal: *45 Menit*, *2 Jam 15 Menit*).
       - **Waktu Selesai Penanganan:** Tanggal dan jam tuntas penanganan WIB.
       - **Teknisi PIC / Tim:** Petugas atau tim yang bertanggung jawab menyelesaikan kendala.
       - **Status Akhir Jaringan:** Indikator *Pulih (Normal UP)*.
       - **Badge Status Tiket:** Menampilkan status *SOLVED* atau *CLOSED* dengan ikon checkmark resmi.
  3. **Penyempurnaan Form Status & RFO Internal:**
     - Pada form *Update Status* di `tickets/show.blade.php`, saat petugas memilih status `RESOLVED` atau `CLOSED`, sistem secara interaktif menampilkan badge panduan RFO dan mengubah placeholder textarea menjadi contoh format pengisian RFO yang informatif.
     - Menambahkan kemampuan memperbarui *Catatan Penyelesaian & RFO* melalui form edit tiket (`UpdateTicketRequest` dan `TicketService::update`).
     - Memperbarui template ringkasan pesan WhatsApp (`Ticket::whatsappSummary()`) agar otomatis menyertakan catatan RFO, durasi gangguan, dan tautan tracking permanen saat tiket ditutup/diselesaikan.
  4. **Pengujian Otomatis (Automated Testing):**
     - Menambahkan 3 skenario tes baru pada `TicketProgressUpdateTest.php`:
       - `test_public_tracking_link_does_not_expire_when_ticket_is_closed_and_displays_rfo`: Memverifikasi tautan tetap aktif (HTTP 200) saat tiket CLOSED dan memverifikasi seluruh komponen RFO dirender dengan benar.
       - `test_whatsapp_summary_contains_rfo_and_tracking_link_for_closed_ticket`: Memverifikasi ringkasan WA memuat data RFO dan tautan permanen.
       - `test_can_update_resolution_note_rfo_via_ticket_edit`: Memverifikasi catatan RFO dapat disunting/diperbarui dengan benar.
     - Seluruh tes (16 tes, 85 assertions) lulus 100%.

---

#### ⏰ Pukul 11:45:00 WIB — Anti-Overflow & Word-Wrap Teks Progres Sharelink
- **Latar Belakang & Kebutuhan:** Pada tampilan sharelink mockup handphone (`/p/{token}`), teks atau catatan progres teknisi yang memuat deretan karakter tanpa spasi (misalnya: tanda seru berulang `!!!!!!!`, URL panjang, alamat IP, dump log, dsb.) mengalami offside meluber keluar dari batas card timeline dan memunculkan horizontal scrollbar yang mengganggu estetika mockup.
- **Tindakan Teknis:**
  1. **Strict Text Break & Wrapping Rules:**
     - Menambahkan properti CSS `overflow-wrap: anywhere !important;`, `word-break: break-word !important;`, dan `word-wrap: break-word !important;` pada seluruh elemen teks di `.phone-content`, `.timeline-card`, kartu RFO, dan judul tiket.
     - Memastikan bahwa setiap deretan karakter tak terputus (unbroken string) otomatis terpotong dan turun baris mengikuti lebar card secara presisi.
  2. **Eliminasi Horizontal Scrollbar:**
     - Menetapkan `overflow-x: hidden !important;` pada `.phone-content` dan `.timeline-container`.
     - Menambahkan `min-width: 0;`, `width: 100%;`, dan `box-sizing: border-box;` pada flex container dan `.timeline-card` agar tidak memicu pemuaian horizontal.
  3. **Penyesuaian Tampilan Internal:**
     - Menerapkan rule word-break serupa pada tampilan detail tiket internal (`tickets/show.blade.php`) di bagian catatan penyelesaian RFO dan riwayat timeline.
  4. **Pengujian Otomatis (Automated Testing):**
     - Menambahkan unit test `test_long_unbroken_text_in_progress_update_has_anti_overflow_classes_and_styles` pada `TicketProgressUpdateTest.php` untuk memverifikasi 100 karakter tak terputus dibungkus dengan rule anti-overflow.
     - Seluruh 17 tes (89 assertions) lulus 100%.

---

#### ⏰ Pukul 12:15:00 WIB — Perbaikan Alignment Teks Timeline Progres & RFO
- **Latar Belakang & Kebutuhan:** Pada tampilan timeline progres perbaikan sharelink client (`/p/{token}`), terjadi bug tampilan visual di mana label `RFO:` dan baris pertama teks catatan bergeser jauh ke kanan (*terdorong offset indentation*), sedangkan baris berikutnya rata kiri, sehingga susunan teks terlihat berantakan dan tidak sejajar.
- **Penyebab Masalah (Root Cause):** Tag Blade `@if` dan `<strong>RFO:</strong>` diletakkan di dalam tag container yang memiliki properti CSS `white-space: pre-wrap;` dengan spasi indentasi Blade (~48-52 spasi). Karakter spasi indentasi template tersebut secara literal dirender oleh browser sebagai spasi kosong di layar, sehingga mendorong teks ke kanan.
- **Tindakan Teknis:**
  1. **Pemisahan Header & Kontainer Teks:**
     - Memisahkan judul header `RFO (Catatan Penyelesaian):` ke dalam elemen tersendiri di baris atas dengan text-align left (`text-start`).
     - Menempatkan isi teks catatan di dalam kontainer teks dedicated tanpa spasi indentasi Blade sebelum variabel.
  2. **Sanitasi String dengan `trim()`:**
     - Menggunakan fungsi `trim()` pada `$event['note']` dan `$ticket->resolution_note` untuk mengeliminasi spasi/newline kosong tak disengaja dari awal dan akhir teks.
  3. **Penegasan Text Alignment Kiri (`text-start` & `text-align: left`):**
     - Menerapkan penegasan `text-align: left;` dan kelas `.text-start` pada `.timeline-card`, kontainer catatan RFO, dan detail penugasan teknisi.
  4. **Pengujian & Verifikasi:**
     - Verifikasi langsung via `curl` pada tiket CLOSED: teks `Root cause:` dan baris berikutnya kini berbaris lurus rata kiri (*left aligned*) sempurna dengan kolom 0 tanpa pergeseran horizontal.
     - Seluruh 17 tes otomatis lulus 100%.

#### ⏰ Pukul 12:30:00 WIB — Konfigurasi Reverse Proxy & Akses Publik Ngrok Sementara
- **Latar Belakang & Kebutuhan:** Tim memerlukan link sementara yang bisa dibuka oleh karyawan (misal lewat HP atau browser luar jaringan kantor) untuk menguji alur pembuatan tiket, update timeline progres, serta tampilan sharelink mockup HP.
- **Tindakan Teknis:**
  1. **Konfigurasi Reverse Proxy Laravel (`trustProxies`):**
     - Menambahkan `$middleware->trustProxies(at: '*');` pada [`bootstrap/app.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/bootstrap/app.php).
     - Menjamin URL asset (CSS/JS), cookie sesi login, verifikasi token CSRF form, dan generator link tracking publik `/p/{token}` secara otomatis menggunakan domain HTTPS publik ngrok, bukan fallback ke `localhost:8000`.
  2. **Aktivasi Tunnel Ngrok:**
     - Menjalankan tunnel ngrok ke port 8000 (`ngrok http 8000 --log=stdout`).
     - Tunneling online menghasilkan URL publik HTTPS yang dapat langsung diakses publik.
  3. **Verifikasi:**
     - Pengujian akses tiket via cURL domain ngrok menghasilkan status HTTP 200 OK.
     - Seluruh 17 unit/feature test Laravel tetap lulus 100%.

---

### 4. Jumat, 09 Oktober 2026

#### ⏰ Pukul 09:40:00 WIB — Standarisasi Nomor Layanan Client Berbasis Angka Murni
- **Latar Belakang & Kebutuhan:** Nomor layanan data pelanggan (client) sebelumnya menggunakan kode ber-prefix (`PLG-XXXXX`, `CP-XXXXX`, `RS-XXXXX`). Manajemen menetapkan standarisasi format nomor layanan client baru yang menggunakan angka murni (numeric fixed pad 5 digit) berdasarkan kategori:
  - **Reseller:** `920210000[nomor urut]` (contoh urut 1: `9202100001`, urut 2: `9202100002`)
  - **Corporate:** `920220000[nomor urut]` (contoh urut 1: `9202200001`, urut 2: `9202200002`)
  - **Home:** `920230000[nomor urut]` (contoh urut 1: `9202300001`, urut 2: `9202300002`)
- **Tindakan Teknis:**
  1. **Konstanta Model Customer ([`app/Models/Customer.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Models/Customer.php)):**
     - Menambahkan konstanta lengkap dengan 0000: `SERVICE_PREFIX_RESELLER = '920210000'`, `SERVICE_PREFIX_CORPORATE = '920220000'`, dan `SERVICE_PREFIX_HOME = '920230000'`.
  2. **Logika Generator Layanan ([`app/Services/CustomerService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/CustomerService.php)):**
     - Memperbarui method `generateServiceNumber($category)` agar menggunakan prefix numerik ber-0000 dan nomor urut langsung (`$prefix . $nextSeq`). Angka `0000` dipastikan selalu tercantum utuh (misal urut 1: `9202100001`, urut 10: `92021000010`).
     - Menjaga independensi nomor urut tiap kategori tanpa bentrok atau terinterupsi saat data lain dihapus.
  3. **Penyesuaian UI & Form:**
     - [`resources/views/customers/_form.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/customers/_form.blade.php): Memperbarui placeholder dan teks form agar mencantumkan angka `0000` secara eksplisit (`misal: 9202100001, 9202200001, 9202300001` dan `920210000[Urut]`).
     - [`resources/views/customers/index.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/customers/index.blade.php): Memperbarui modal Import Spreadsheet pada opsi 'Kategori Default' (`Home (Nomor layanan: 9202300001, 9202300002, dst)`) dan keterangan tabel panduan.
     - [`app/Services/CustomerImportService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/CustomerImportService.php): Memperbarui sampel data template Excel & CSV.
  4. **Migrasi Database Legacy Data ([`database/migrations/2026_10_23_100001_update_customer_service_numbers_to_numeric.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/database/migrations/2026_10_23_100001_update_customer_service_numbers_to_numeric.php)):**
     - Dibuat migration otomatis yang secara aman mendeteksi jika ada data client lama berformat `RS-`, `CP-`, atau `PLG-` untuk dikonversi ke format angka `920210000X`, `920220000X`, `920230000X`.
  5. **Pengujian & Verifikasi:**
     - Memperbarui [`tests/Feature/CustomerImportTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/CustomerImportTest.php) dan [`tests/Feature/CustomerServiceNumberTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/CustomerServiceNumberTest.php) termasuk verifikasi preservasi `0000` untuk urutan multi-digit.
     - Seluruh 23 test Laravel (100 assertions) lulus 100%.

#### ⏰ Pukul 10:35:00 WIB — Portal Baru Teknisi & Registrasi Role Teknisi Mandiri
- **Latar Belakang & Kebutuhan:** Dibutuhkan portal khusus untuk tim Teknisi Lapangan guna memantau dan menangani Work Order aktif, tiket gangguan perbaikan, serta pelaporan kunjungan lapangan (Field Visit Report). Pengaturan hak akses (role & permission) Teknisi diminta disediakan agar dapat dikonfigurasi dan diisi sendiri oleh tim/admin.
- **Tindakan Teknis:**
  1. **Registrasi Permission & Role Baru:**
     - Menambahkan permission `teknisi.portal.view` (`Akses Portal Teknisi`) dengan modul `teknisi`.
     - Menambahkan role baru `Teknisi` (`slug: teknisi`, `name: Teknisi`) pada database via migrasi [`database/migrations/2026_10_24_100001_create_teknisi_role_and_permission.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/database/migrations/2026_10_24_100001_create_teknisi_role_and_permission.php) dan [`database/seeders/RolePermissionSeeder.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/database/seeders/RolePermissionSeeder.php).
     - Role **Teknisi** kini langsung tampil di tabel halaman **Roles & Permissions** (`/roles`) lengkap dengan tombol **Kelola Permission**, sehingga user dapat mencentang dan menyesuaikan permission untuk role Teknisi secara mandiri.
  2. **Controller & Route Portal Teknisi:**
     - Membuat [`app/Http/Controllers/PortalTeknisiController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/PortalTeknisiController.php) yang memuat query ringkasan tugas: Work Order aktif (Scheduled & In Progress), Tiket aktif tim teknisi, Laporan Kunjungan lapangan, serta status presensi/kehadiran hari ini.
     - Mendaftarkan route `/portal-teknisi` (`portal-teknisi.index`) pada [`routes/web.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/routes/web.php).
  3. **Tampilan Antarmuka Portal Teknisi:**
     - Membuat view [`resources/views/portal-teknisi/index.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/portal-teknisi/index.blade.php) dengan banner profil teknisi, 4 stat card ringkasan tugas, tabel Work Order terjadwal, tabel tiket gangguan, riwayat kunjungan lapangan, dan pintasan presensi/aset.
     - Sesuai permintaan, banner profil teknisi disederhanakan dengan tidak menampilkan teks `Role: Teknisi`, melainkan hanya menampilkan badge nama **Tim** saja agar lebih bersih dan fokus pada operasional tim.
     - Menambahkan menu **Portal Teknisi** (icon `bi-tools`) pada sidebar Blade [`resources/views/partials/sidebar.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/partials/sidebar.blade.php) dan Vue layout [`resources/js/Layouts/AppLayout.vue`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/js/Layouts/AppLayout.vue).
     - Menghubungkan role `teknisi` agar dapat dipilih sebagai teknisi pelaksana Work Order pada [`app/Http/Controllers/WorkOrderController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/WorkOrderController.php).
  4. **Pengujian & Verifikasi:**
     - Dibuatkan automated test [`tests/Feature/PortalTeknisiTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/PortalTeknisiTest.php) (6 test) untuk memverifikasi hak akses Super Admin, hak akses user Teknisi, proteksi 403 untuk guest/unauthorized, ketiadaan label `Role: Teknisi`, kemunculan role Teknisi di halaman Roles, serta kemampuan pengelolaan permission-nya.
     - Seluruh **29 test Laravel (118 assertions) lulus 100%**.

#### ⏰ Pukul 11:15:00 WIB — Perbaikan Vertical Scrollbar & Responsivitas Sidebar
- **Latar Belakang & Kebutuhan:** Menu sidebar pada aplikasi bertambah panjang seiring penambahan menu baru (seperti Portal Teknisi, Work Order, Laporan Kunjungan, dll). Menu bagian bawah (Laporan Kunjungan, Users, Roles & Permissions, Template WhatsApp, Audit Trail, Pengaturan, API Token) terpotong dan tidak bisa di-scroll secara vertikal pada layar desktop maupun mobile.
- **Tindakan Teknis:**
  1. **Aktivasi Scroll Vertikal Terisolasi:**
     - Pada [`resources/views/layouts/app.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/layouts/app.blade.php) dan [`resources/js/Layouts/AppLayout.vue`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/js/Layouts/AppLayout.vue), class `.noc-sidebar` diubah dengan menetapkan `height: 100vh; max-height: 100vh; overflow-y: auto; overflow-x: hidden; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; display: flex; flex-direction: column;`.
  2. **Sticky Header Logo Brand:**
     - Menjadikan container `.noc-sidebar .brand` tetap menempel di atas (`position: sticky; top: 0; background: #0b1d3a; z-index: 2; flex-shrink: 0;`) sehingga saat menu di-scroll ke bawah, logo REPLAY BOS tetap terlihat rapi dan elegan.
  3. **Custom Modern Scrollbar & Bottom Spacing:**
     - Mengimplementasikan sleek webkit scrollbar modern (lebar 6px dengan thumb semi-transparan yang halus) serta standar `scrollbar-width: thin`.
     - Memberikan `padding-bottom: 2.5rem;` pada container `.noc-sidebar .nav` agar item menu paling bawah (API Token, Pengaturan, dll) memiliki ruang nafas yang nyaman dan tidak mepet di dasar layar.
  4. **Pengujian & Kompilasi:**
     - Build aset Vite (`npm run build`) berhasil tanpa error.
     - Seluruh unit & feature test Laravel (`php artisan test`) lulus 100% (29 passed, 118 assertions).

#### ⏰ Pukul 11:22:00 WIB — Fitur Kembalikan / Tutup Sidebar Mobile (Close Button & Backdrop)
- **Latar Belakang & Kebutuhan:** Pada resolusi layar ponsel/mobile, sidebar tersembunyi secara default. Ketika tombol hamburger diklik, sidebar terbuka memenuhi layar, namun tidak ada fitur untuk mengembalikan/menutup sidebar kembali selain refresh halaman.
- **Tindakan Teknis:**
  1. **Tombol Tutup (Icon X) di Header Sidebar:**
     - Menambahkan tombol `<button id="noc-sidebar-close" ...><i class="bi bi-x-lg"></i></button>` di header `.brand` pada [`resources/views/partials/sidebar.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/partials/sidebar.blade.php) dan [`resources/js/Layouts/AppLayout.vue`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/js/Layouts/AppLayout.vue). Tombol ini otomatis hanya muncul di mobile (`d-lg-none`) dan tersembunyi di desktop.
  2. **Backdrop / Overlay Semi-Transparan (Klik Luar):**
     - Menambahkan elemen `#noc-sidebar-backdrop` dengan styling `background: rgba(7, 18, 40, 0.65); backdrop-filter: blur(2px); z-index: 1035;`. Ketika sidebar terbuka di mobile, backdrop otomatis muncul. Pengguna cukup mengklik area luar manapun untuk menutup sidebar secara instan.
  3. **Event Listener Interaktif & Keyboard Shortcut:**
     - Menambahkan listener event tombol `Escape` pada keyboard untuk menutup sidebar.
     - Menambahkan auto-close saat salah satu link navigasi (`.nav-link`) diklik pada resolusi mobile.
     - Mencegah scroll latar belakang halaman (`document.body.style.overflow = 'hidden'`) saat sidebar sedang terbuka di mobile untuk kenyamanan interaksi sentuh.
  4. **Kompilasi & Pengujian:**
     - Build aset Vite (`npm run build`) sukses tanpa kendala.
     - Seluruh 29 automated test (`php artisan test`) lulus 100% (118 assertions).

#### ⏰ Pukul 11:40:00 WIB — Modul Upgrade/Downgrade Paket & Otomatisasi Work Order Finance
- **Latar Belakang & Kebutuhan:** Diperlukan fitur terstruktur untuk pengajuan Upgrade / Downgrade paket layanan pelanggan yang diajukan oleh Marketing / NOC kepada bagian Finance. Jika pengajuan disetujui (approved) oleh Finance, sistem harus secara otomatis men-generate nomor Work Order Upgrade untuk penanganan teknisi lapangan tanpa perlu input ulang manual.
- **Tindakan Teknis:**
  1. **Database & Migrasi ([`database/migrations/2026_10_25_100001_create_package_change_requests_table.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/database/migrations/2026_10_25_100001_create_package_change_requests_table.php)):**
     - Membuat tabel `package_change_requests` dengan atribut: `request_number` (`REQ-UPG-XXXXXX` / `REQ-DWN-XXXXXX`), `customer_id`, `type` (`UPGRADE`/`DOWNGRADE`), `current_service_package_id`, `current_price`, `target_service_package_id`, `target_price`, `notes`, `status` (`PENDING`/`APPROVED`/`REJECTED`/`CANCELLED`), `requested_by`, `approved_by`, `approved_at`, `finance_notes`, dan `work_order_id`.
     - Mendaftarkan permission `package.change.view`, `package.change.create`, dan `package.change.approve` serta menghubungkannya ke role `super-admin`, `finance`, `marketing`, dan `noc-admin`.
  2. **Model & Layanan Bisnis:**
     - Model [`app/Models/PackageChangeRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Models/PackageChangeRequest.php) dengan relasi lengkap ke Customer, ServicePackage, User, dan WorkOrder.
     - Service [`app/Services/PackageChangeRequestService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/PackageChangeRequestService.php) menangani workflow pengajuan, approval/reject Finance, dan pembatalan.
     - Menambahkan method `createFromPackageChange()` pada [`app/Services/WorkOrderService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/WorkOrderService.php) dan tipe `UPGRADE`/`DOWNGRADE` pada [`app/Models/WorkOrder.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Models/WorkOrder.php).
     - **Saat Finance menyetujui (Approve):** Sistem secara instan dalam 1 transaksi DB:
       - Mengubah status request menjadi `APPROVED`.
       - Men-generate nomor Work Order (misal: `WO-00000X`) dengan tipe `UPGRADE`/`DOWNGRADE`.
       - Memperbarui paket dan tarif langganan di master data `Customer`.
       - Menghubungkan nomor WO ke record pengajuan dan mencatat audit trail.
  3. **Antarmuka & Navigasi:**
     - Controller [`app/Http/Controllers/PackageChangeRequestController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/PackageChangeRequestController.php) & FormRequest [`app/Http/Requests/StorePackageChangeRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Requests/StorePackageChangeRequest.php).
     - Menambahkan menu **Upgrade/Downgrade** (`<i class="bi bi-arrow-down-up"></i>`) pada sidebar Blade dan Vue layout.
     - Membuat halaman Index [`resources/views/package-changes/index.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/package-changes/index.blade.php) lengkap dengan 4 stat card, filter status, tabel permohonan, modal approval & rejection Finance.
     - Membuat form pengajuan [`resources/views/package-changes/create.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/package-changes/create.blade.php) dengan deteksi otomatis paket saat ini vs paket tujuan.
     - Membuat halaman detail [`resources/views/package-changes/show.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/package-changes/show.blade.php) dengan kartu komparasi paket dan tautan langsung ke Work Order yang digenerate.
  4. **Pengujian & Kompilasi:**
     - Dibuatkan automated feature test [`tests/Feature/PackageChangeRequestTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/PackageChangeRequestTest.php) (5 skenario).
     - Seluruh **34 test Laravel (141 assertions) lulus 100%**.
     - Build aset Vite (`npm run build`) sukses tanpa kendala.

---

#### ⏰ Pukul 12:45:00 WIB — Penyempurnaan Form Pengajuan Upgrade/Downgrade (Kategori Client, Ketik Paket, & Hapus Form Biaya Baru)
- **Latar Belakang & Kebutuhan:**
  - Pengguna meminta penyesuaian pada formulir pengajuan upgrade/downgrade:
    1. **Pemilihan Client:** Disaring berdasarkan kategori pelanggan yaitu **Reseller**, **Corporate**, atau **Home**.
    2. **Pemilihan Paket:** Lebih fleksibel dengan cara **diketik langsung** (free-text / custom package) dan dilengkapi saran autocomplete katalog jika ingin memilih paket standar.
    3. **Penghapusan Form Biaya Langganan Baru:** Input biaya baru dihilangkan dari form pengajuan Marketing/NOC, karena penetapan tarif baru merupakan wewenang Finance saat persetujuan.
- **Tindakan Teknis:**
  1. **Struktur Data & Migrasi:**
     - Menyesuaikan tabel `package_change_requests` agar kolom `target_service_package_id` bersifat opsional (`nullable`), menambahkan string `target_package_name` (nama paket yang diketik) dan `current_package_name`.
     - Memperbarui model [`app/Models/PackageChangeRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Models/PackageChangeRequest.php) dengan accessor `targetPackageLabel()` dan `currentPackageLabel()`.
  2. **Form Request & Service Bisnis:**
     - [`app/Http/Requests/StorePackageChangeRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Requests/StorePackageChangeRequest.php): Mengganti validasi `target_service_package_id` wajib menjadi `target_package` string, serta menjadikan `target_price` nullable (tidak wajib dari form).
     - [`app/Services/PackageChangeRequestService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/PackageChangeRequestService.php): Otomatis mencocokkan nama paket yang diketik dengan katalog `service_packages` bila ada, atau menyimpannya sebagai paket kustom. Saat Finance menyetujui, Finance dapat secara opsional menginput nominal biaya langganan baru yang akan diupdate ke master pelanggan.
     - [`app/Services/WorkOrderService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/WorkOrderService.php): Catatan Work Order otomatis menggunakan nama paket yang diketik (`Paket Lama -> Paket Baru`).
  3. **Antarmuka & Pengalaman Pengguna (UI/UX):**
     - [`resources/views/package-changes/create.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/package-changes/create.blade.php):
       - Menambahkan **Filter Kategori Client** berbentuk button pills interaktif: **Semua Client**, **Reseller (92021...)**, **Corporate (92022...)**, dan **Home (92023...)**.
       - Mengelompokkan opsi dropdown pelanggan menggunakan `<optgroup>` berdasarkan kategori. Pilihan pelanggan akan terfilter otomatis saat tombol kategori ditekan.
       - Menyediakan input teks bebas untuk **Paket Baru Tujuan** dengan `<datalist>` rekomendasi dari katalog paket.
       - **Menghapus form input Biaya Langganan Baru per Bulan (Rp)** dari formulir pengajuan.
     - [`resources/views/package-changes/index.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/package-changes/index.blade.php) & [`show.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/package-changes/show.blade.php): Menampilkan nama paket yang diketik, status biaya (jika belum ditetapkan Finance ditampilkan *"Ditetapkan Finance"*), dan menyediakan input penyesuaian tarif opsional di dalam Modal Persetujuan Finance.
  4. **Pengujian:**
     - Penambahan skenario tes pada [`tests/Feature/PackageChangeRequestTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/PackageChangeRequestTest.php) untuk pengajuan dengan paket yang diketik bebas maupun dari katalog.
     - Seluruh **35 tes fitur (144 assertions)** lulus 100%.

---

#### ⏰ Pukul 13:30:00 WIB — Menu Resource Database & Monitoring cPanel Server
- **Latar Belakang & Kebutuhan:** Kebutuhan tim administrator NOC untuk memantau kapasitas database MySQL/MariaDB yang dihosting di cPanel secara langsung dari dashboard sistem, termasuk ukuran database, pemakaian disk hosting, koneksi aktif, rincian tabel, serta fasilitas optimasi tabel (`OPTIMIZE TABLE`) untuk membersihkan data overhead/fragmentasi.
- **Tindakan Teknis:**
  1. **Migrasi Permission & Otorisasi:**
     - Membuat migrasi [`database/migrations/2026_10_26_100001_create_database_monitor_permission.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/database/migrations/2026_10_26_100001_create_database_monitor_permission.php) untuk mendaftarkan permission `database.monitor` dan mengaitkannya ke role `super-admin` dan `noc-admin`.
     - Memperbarui [`database/seeders/RolePermissionSeeder.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/database/seeders/RolePermissionSeeder.php) dan [`app/Providers/AppServiceProvider.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Providers/AppServiceProvider.php) dengan gate definition `Gate::define('database.monitor', ...)`.
  2. **Service Layer Monitoring Database:**
     - Membuat [`app/Services/DatabaseResourceService.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Services/DatabaseResourceService.php) yang membaca data real-time:
       - **Ukuran Database:** Ukuran data, indeks, total ukuran, dan sisa overhead fragmentasi via query `information_schema.tables`.
       - **Koneksi Database:** Thread koneksi aktif vs `max_connections` (limit database server cPanel).
       - **Disk Storage Hosting:** Kapasitas disk yang terpakai, free space, dan total storage via `disk_total_space` dan `disk_free_space`.
       - **Rincian Variabel Server:** Versi MariaDB/MySQL, buffer pool size, total query (Questions), slow queries, uptime, dan charset/collation.
       - **Rincian Lingkungan PHP:** Memory limit, memory usage, real memory, serta max execution time.
       - **Daftar Tabel Terperinci:** Rincian 48+ tabel database lengkap dengan engine, row count, ukuran data, ukuran indeks, overhead, dan status fragmentasi.
       - **Aksi Optimasi:** Menjalankan perintah `OPTIMIZE TABLE` per tabel maupun secara menyeluruh (*Optimize All Tables*).
       - **Dukungan Fallback Multi-Driver:** Kompatibel dengan driver SQLite saat eksekusi automated unit test.
  3. **Controller & Routing:**
     - Membuat [`app/Http/Controllers/DatabaseResourceController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/DatabaseResourceController.php) dengan method:
       - `index()`: Menampilkan dashboard monitoring database lengkap.
       - `optimize()`: Optimasi tabel tunggal dengan validasi nama tabel yang aman.
       - `optimizeAll()`: Optimasi seluruh tabel database sekaligus.
       - `jsonStats()`: Endpoint data JSON untuk refresh live data.
     - Mendaftarkan rute pada [`routes/web.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/routes/web.php) di bawah prefix `/resources` dengan proteksi middleware `auth` dan gate `can:database.monitor`.
  4. **Antarmuka (UI/UX) & Navigasi:**
     - Menambahkan menu **"Resource Database"** dengan ikon database modern pada navigasi sidebar [`resources/views/partials/sidebar.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/partials/sidebar.blade.php) dan [`resources/js/Layouts/AppLayout.vue`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/js/Layouts/AppLayout.vue).
     - Membuat tampilan dashboard modern di [`resources/views/database-resources/index.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/database-resources/index.blade.php) dengan 4 Stat Cards utama, panel rincian server & runtime PHP, live search & filter tabel, serta modal aksi optimasi.
  5. **Pengujian Otomatis:**
     - Membuat [`tests/Feature/DatabaseResourceTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/DatabaseResourceTest.php) (5 skenario tes).
     - Menjalankan seluruh test suite: **41 tes fitur lulus 100% (174 assertions)**.

---

#### ⏰ Pukul 14:25:00 WIB — Penyelarasan Nomor Layanan Reseller & Command Tool Otomatis
- **Latar Belakang & Kebutuhan:** Sebelumnya terdapat 2 pelanggan Reseller yang dipindahkan ke kategori Corporate (seperti *Maman Suparman*), sehingga nomor urut `9202100005` menjadi kosong/loncat di database dan menyebabkan 64 pelanggan Reseller sesudahnya tergeser hingga nomor `92021000069`. Dibutuhkan penyelarasan agar nomor layanan Reseller kembali berurutan rapi dari `9202100001` sampai `92021000068`.
- **Tindakan Teknis:**
  1. **Pembuatan Command Artisan Penyelaras:**
     - Membuat [`app/Console/Commands/AlignCustomerServiceNumbers.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Console/Commands/AlignCustomerServiceNumbers.php) dengan signature:
       `php artisan customers:align-service-numbers {--category=RESELLER} {--dry-run}`
     - Mendukung simulasi perubahan (`--dry-run`), transaksi database atomik (`DB::beginTransaction`), dan pencatatan audit trail otomatis.
  2. **Eksekusi Penyelarasan Database:**
     - Menjalankan command penyelarasan pada database lokal.
     - Sebanyak **64 data pelanggan Reseller** berhasil diselaraskan kembali mulai dari ID 20 (*Aang Suhaya*: `9202100006` -> `9202100005`) hingga ID 83 (*Taopik Hidayat*: `92021000069` -> `92021000068`).
     - Pelanggan Corporate (2 pelanggan: `9202200001` & `9202200002`) tetap terjaga dan selaras.
     - Nomor layanan berikutnya untuk pendaftaran Reseller baru kini otomatis menghasilkan `92021000069` secara presisi.
  3. **Pengujian:**
     - Seluruh **41 tes fitur lulus 100% (174 assertions)**.

---

#### ⏰ Pukul 14:45:00 WIB — Penguncian Readonly & Pemformatan Titik Harga Satuan Surat Penawaran
- **Latar Belakang & Kebutuhan:** Di menu **Surat Penawaran** (`/quotations/create` & edit), input *Harga Satuan* sebelumnya berupa input teks biasa yang bisa diedit sembarangan oleh tim marketing tanpa titik pemisah ribuan. Dibutuhkan penguncian agar harga satuan tidak dapat diedit manual (*readonly*) dan nilainya otomatis diformat dengan tanda titik ribuan (misalnya `3.000.000`) sesuai harga katalog paket yang dipilih.
- **Tindakan Teknis:**
  1. **Tampilan & Logika Input Dinamis ([create.blade.php](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/quotations/create.blade.php)):**
     - Menambahkan fungsi pemformatan nominal ribuan `formatNominal(n)` berbasis `toLocaleString('id-ID')`.
     - Mengubah elemen input harga satuan menjadi input bertipe teks yang bersifat `readonly`, diberi latar belakang `bg-light`, kursor `not-allowed`, dan menampilkan nilai bertitik (contoh: `3.000.000`).
     - Menyediakan `<input type="hidden" class="item-price" name="items[idx][unit_price]">` untuk menyimpan nilai numerik murni asli (`3000000`).
     - Sinkronisasi otomatis saat dropdown *Paket (opsional)* dipilih: nama item, nilai input tersembunyi, dan tampilan teks bertitik langsung terupdate otomatis beserta subtotal, PPN, dan total penawaran.
  2. **Sanitasi Data di Backend Form Request:**
     - Menambahkan method `prepareForValidation()` pada [`StoreQuotationRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Requests/StoreQuotationRequest.php) dan [`UpdateQuotationRequest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Requests/UpdateQuotationRequest.php) untuk membersihkan tanda titik atau koma pada string `unit_price` sebelum aturan validasi `numeric` dijalankan.
     - Mempertahankan array `old('items')` pada [`QuotationController.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/app/Http/Controllers/QuotationController.php) agar data item penawaran tidak hilang jika terjadi error validasi di form lain.
  3. **Pengujian Otomatis:**
     - Membuat [`tests/Feature/QuotationUnitPriceTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/QuotationUnitPriceTest.php) (4 pengujian).
     - Seluruh **45 tes fitur lulus 100% (185 assertions)**.

---

#### ⏰ Pukul 15:45:00 WIB — Standardisasi Label BPJS Ketenagakerjaan & BPJS Kesehatan
- **Latar Belakang & Kebutuhan:** Penyelarasan istilah penamaan dokumen BPJS pada modul Karyawan dan Portal Karyawan agar lebih resmi dan mudah dipahami:
  - `Nomor JMO (BPJS TK)` diubah menjadi **`Nomor BPJS Ketenagakerjaan`**
  - `Nomor KPJ (Jamsostek)` diubah menjadi **`Nomor BPJS Kesehatan`**
- **Tindakan Teknis:**
  1. **Halaman Detail Karyawan ([show.blade.php](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/employees/show.blade.php)):**
     - Memperbarui label kartu *Data Pribadi* dari `Nomor JMO (BPJS TK)` menjadi `Nomor BPJS Ketenagakerjaan` dan `Nomor KPJ (Jamsostek)` menjadi `Nomor BPJS Kesehatan`.
  2. **Formulir Edit & Tambah Karyawan ([_form.blade.php](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/employees/_form.blade.php)):**
     - Memperbarui label form input beserta teks placeholder `Nomor BPJS Ketenagakerjaan` dan `Nomor BPJS Kesehatan`.
  3. **Tabel & Modal Data Karyawan ([index.blade.php](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/employees/index.blade.php)):**
     - Memperbarui header kolom tabel menjadi `BPJS KETENAGAKERJAAN` dan `BPJS KESEHATAN`.
     - Memperbarui placeholder pencarian kata kunci dan modal popup tambah/edit karyawan.
  4. **Portal Karyawan ([Index.vue](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/js/Pages/PortalKaryawan/Index.vue) & [index.blade.php](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/portal-karyawan/index.blade.php)):**
     - Menyesuaikan label pada ringkasan profil karyawan mandiri dan mengompilasi ulang aset via `npm run build`.
  5. **Pengujian:**
     - Seluruh **45 tes fitur lulus 100% (185 assertions)**.

---

#### ⏰ Pukul 16:45:00 WIB — Detail Absensi: Foto Live, Watermark Logo & Timestamp Replay, Alamat & GPS
- **Latar Belakang & Kebutuhan:**
  - Pengguna meminta agar sistem absensi mandiri karyawan dilengkapi bukti kehadiran nyata berupa foto selfie langsung (*live camera*), alamat lokasi, serta titik koordinat GPS yang disimpan ke dalam database.
  - Foto absensi harus memiliki ciri khas resmi Replay BOS dengan membubuhkan **watermark logo Replay** dan **timestamp waktu real-time** langsung di atas foto saat pengambilan.
- **Tindakan Teknis:**
  1. **Migrasi Database (`2026_10_27_100001_add_photo_and_address_to_attendances_table.php`):**
     - Menambahkan kolom `check_in_photo_path`, `check_in_address`, `check_out_photo_path`, dan `check_out_address` pada tabel `attendances`.
     - Kolom titik koordinat `check_in_lat`, `check_in_lng`, `check_out_lat`, dan `check_out_lng` telah terintegrasi secara presisi.
  2. **Model Layer (`app/Models/Attendance.php`):**
     - Memperbarui `$fillable` dengan field path foto dan alamat check-in & check-out.
     - Menambahkan helper method `checkInPhotoUrl()` dan `checkOutPhotoUrl()` untuk URL publik via symlink storage.
  3. **Service Layer (`app/Services/AttendanceService.php`):**
     - Menambahkan method `savePhoto(mixed $photoData, string $type, int $employeeId)`: mendukung Base64 Data URL (`data:image/jpeg;base64,...`) dan `UploadedFile`. File disimpan secara aman di disk publik pada folder `attendances/{Y-m}/`.
     - Memperbarui `checkIn()` dan `checkOut()` untuk menyimpan data foto, alamat lokasi, koordinat GPS, serta audit log sistem.
  4. **Mesin Watermark HTML5 Canvas & Live Viewfinder:**
     - Menggunakan `navigator.mediaDevices.getUserMedia` untuk live camera stream dengan panduan wajah selfie.
     - Mengambil logo Replay dari `public/images/branding/logo-replay-net.png`.
     - Menggambar watermark berlapis di atas Canvas:
       - **Top Header:** Overlay gelap gradasi, Logo Replay (56x56 px), teks "REPLAY BOS", sub-judul "PT REPLAY NETWORK INDONESIA • OFFICIAL ATTENDANCE", dan badge status pill (`● CHECK-IN` hijau atau `■ CHECK-OUT` merah).
       - **Bottom Banner:** Overlay gelap gradasi pekat, garis aksen, nama karyawan & NIK & jabatan, waktu real-time WIB lengkap (contoh: *Jumat, 09 Oktober 2026 • 16:45:00 WIB*), koordinat GPS akurat, alamat hasil reverse geocoding OpenStreetMap Nominatim, dan label verifikasi resmi Replay BOS.
     - Menyediakan tombol jepret foto, opsi *Foto Ulang*, serta fallback upload file kamera jika izin webcam ditolak.
  5. **Antarmuka Pengguna (UI/UX):**
     - **Portal Karyawan Vue / Inertia (`resources/js/Pages/PortalKaryawan/Index.vue`):** Kartu "Absensi Hari Ini" menampilkan ringkasan check-in & check-out, thumbnail foto absensi ber-watermark, teks alamat, link Google Maps, modal kamera interaktif, dan modal Lightbox zoom foto resolusi tinggi.
     - **Portal Karyawan Blade (`resources/views/portal-karyawan/index.blade.php`):** Fungsionalitas setara lengkap dengan modal kamera live dan watermark.
     - **Absensi Mandiri Blade (`resources/views/attendance/self.blade.php`):** Tampilan kamera live dan watermark di halaman `/absensi`.
     - **Laporan Absensi HR / Admin (`resources/views/attendance/index.blade.php`):** Kolom foto thumbnail check-in & check-out, alamat lokasi, dan link Google Maps dengan modal Lightbox zoom inspeksi bukti kehadiran.
  6. **Pengujian & Kompilasi:**
     - Dibuat automated test lengkap [`tests/Feature/AttendanceDetailPhotoAndLocationTest.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/tests/Feature/AttendanceDetailPhotoAndLocationTest.php) (3 assertions baru lulus).
     - Kompilasi aset Vite sukses tanpa error via `npm run build`.
     - Seluruh **48 tes fitur sistem lulus 100% (216 assertions)**.

---

#### ⏰ Pukul 17:05:00 WIB — Perbaikan Error Pembukaan Foto Hasil Absensi (toFixed on String)
- **Latar Belakang & Gejala Error:**
  - Saat karyawan mengklik thumbnail foto absensi di halaman Portal Karyawan (`/portal-karyawan`), modal lightbox gagal terbuka dan browser console menampilkan error:
    ```
    TypeError: w.value.lat.toFixed is not a function
        at app-BWFIHrEV.js:98:55325
    ```
- **Penyebab Masalah (Root Cause):**
  - Pada model `Attendance.php`, kolom koordinat `check_in_lat`, `check_in_lng`, `check_out_lat`, dan `check_out_lng` di-cast menggunakan tipe `'decimal:7'`.
  - Secara default pada Laravel Eloquent, cast tipe `decimal` mengembalikan nilai berupa **string** (misal `"-6.2087634"`) untuk mencegah kehilangan presisi floating point PHP.
  - Pada komponen Vue (`Index.vue`), modal lightbox memanggil langsung `.toFixed(6)` pada properti `activePhotoModal.lat` dan `activePhotoModal.lng`. Karena tipe data aktualnya adalah string, pemanggilan `.toFixed()` menghasilkan `TypeError: toFixed is not a function`.
- **File Terkait:**
  - [`resources/js/Pages/PortalKaryawan/Index.vue`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/js/Pages/PortalKaryawan/Index.vue)
- **Tindakan Teknis & Solusi:**
  1. Menambahkan helper function terpusat `formatCoordinate(coord, precision = 6)` yang secara aman menangani nilai `null`, `undefined`, `number`, maupun `string` dengan konversi `Number(coord)` terlebih dahulu.
  2. Memperbarui antarmuka TypeScript `TodayAttendance`, `AttendanceHistoryItem`, serta reactive state `activePhotoModal` dan `attendanceLocation` agar menerima tipe `number | string | null`.
  3. Mengganti semua pemanggilan langsung `.toFixed()` pada template Vue dengan `formatCoordinate(...)` (modal lightbox, modal kamera, dan ringkasan card absensi hari ini).
  4. Menjalankan kompilasi aset frontend via `npm run build` sehingga bundle Javascript produksi terbarui bersih.
- **Hasil Verifikasi:**
  - Build frontend Vite sukses tanpa error (`app-DW1srdG-.js` 381.06 kB).
  - Seluruh pengujian fitur backend (48 feature & unit tests) lulus 100%.
  - Modal lightbox foto absensi kini dapat dibuka lancar dengan preview gambar resolusi penuh, alamat lengkap, dan koordinat GPS rapi.

---

### 5. Minggu, 11 Oktober 2026

#### ⏰ Pukul 01:25:00 WIB — Redesign UI/UX Modal Absensi Check-in & Standardisasi Watermark Resmi Replay
- **Latar Belakang & Kebutuhan:**
  - Melakukan redesign dan perbaikan UI/UX pada menu Absensi Check-in (Masuk) agar tampak minimalis, elegan, natural, dan berstandar aplikasi enterprise production yang siap digunakan karyawan sehari-hari.
  - Menghilangkan elemen visual dekoratif yang berlebihan (*AI slop*), seperti garis oval putus-putus panduan wajah (*face guide*), banner disclaimer di bawah viewfinder, teks penjelasan berulang, serta tombol dengan warna ungu yang tidak konsisten.
  - Menghapus sepenuhnya fitur pemilihan gambar/unggah file lokal (`File/Galeri`) dari menu absensi agar foto absensi wajib berasal dari pengambilan langsung kamera perangkat.
  - Memperbarui sistem watermark pada foto agar bersih, profesional, proporsional, dan permanen, dengan ketentuan ketat: hanya memuat logo resmi Replay, nama perusahaan `PT. Replay Inti Media`, timestamp WIB aktual saat pengambilan foto, alamat lokasi, dan koordinat GPS.
- **File Terkait:**
  1. [`resources/js/Pages/PortalKaryawan/Index.vue`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/js/Pages/PortalKaryawan/Index.vue) (Komponen utama Vue 3 Inertia)
  2. [`resources/views/portal-karyawan/index.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/portal-karyawan/index.blade.php) (Tampilan hybrid Blade Portal Karyawan)
  3. [`resources/views/attendance/self.blade.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/resources/views/attendance/self.blade.php) (Tampilan Blade Absensi Mandiri `/absensi`)
- **Tindakan Teknis & Solusi:**
  1. **Redesign Antarmuka Modal Absensi:**
     - Header modal berlatar putih bersih dengan logo resmi Replay (`/images/branding/logo-replay-net.png`), judul minimalis "Absensi Check-in" (atau "Absensi Check-out"), dan tombol tutup bersih. Subtitle dekoratif dihilangkan.
     - Informasi lokasi pengguna dikemas dalam kartu netral minimalis dengan ikon pin hijau Replay, alamat hasil reverse geocoding, koordinat GPS, serta tombol segarkan (*refresh*) titik koordinat.
     - Viewfinder kamera live berasio 4:3 dengan background gelap elegan (`#0f172a`), indikator status aktif minimalis (`LIVE` dengan titik merah berkedip halus), tanpa garis oval panduan dan tanpa teks penjelas di bawah frame.
     - Tombol aksi disederhanakan menjadi satu tombol utama hijau Replay ("Ambil Foto"), yang berubah menjadi "Ambil Ulang Foto" (*outline secondary*) setelah foto berhasil dijepret.
     - Kolom catatan kerja opsional dan tombol aksi footer ("Batal" & "Simpan & Check-in") dengan status *loading* dan proteksi pencegahan klik ganda (*anti double-submit*).
  2. **Penghapusan Total Fitur Upload File/Galeri:**
     - Tombol `File/Galeri` dan input file (`<input type="file">`) telah dihapus secara menyeluruh dari template HTML dan Vue.
     - Handler JavaScript dan ref terkait (`fileInputRef`, `handleFileUpload`, `handleBladeFileUpload`, `handleSelfFileUpload`) telah dibersihkan sepenuhnya dari kode program.
     - Penanganan error izin kamera diperbarui dengan opsi coba lagi (*retry*) tanpa menyediakan opsi upload file alternatif.
  3. **Standardisasi Watermark Resmi Replay (Canvas 2D):**
     - Watermark diposisikan di bagian bawah foto dengan gradient overlay gelap semi-transparan tipis (`height - 116px`) yang elegan sehingga teks selalu kontras dan terbaca jelas pada latar terang maupun gelap tanpa menutupi wajah karyawan.
     - Komponen watermark secara ketat hanya memuat 6 elemen resmi:
       1. Logo resmi Replay di sisi kiri bawah (`/images/branding/logo-replay-net.png`, 64x64 px).
       2. Teks perusahaan: `PT. Replay Inti Media` (bold, putih).
       3. Waktu pengambilan foto dalam zona waktu WIB aktual (contoh: *Minggu, 11 Oktober 2026 · 00:19:32 WIB*).
       4. Alamat lokasi hasil geocoding (terpotong rapi dengan ellipsis jika melebihi lebar frame).
       5. Titik koordinat GPS (`GPS: lat, lng`).
       6. Overlay gelap transparan pelindung keterbacaan teks.
     - Seluruh elemen yang dilarang (teks "REPLAY BOS", badge CHECK-IN/CHECK-OUT, header watermark atas, slogan, dan teks promosi) telah dihilangkan.
     - Watermark dibakar (*burned-in*) permanen ke dalam format Base64 JPEG yang dikirim ke server dan tersimpan di database/storage.
  4. **Kompilasi & Pengujian:**
     - Aset frontend Vite berhasil dikompilasi ulang (`npm run build`).
     - Seluruh 48 automated test PHPUnit lulus 100% (216 assertions).

---

#### ⏰ Pukul 23:36:00 WIB — Pembuatan Bundle Arsip vendor.zip untuk Kesiapan Deployment cPanel Hosting
- **Latar Belakang & Kebutuhan:**
  - Persiapan deployment dan sinkronisasi source code aplikasi Replay NOC ke server production / cPanel hosting yang tidak memiliki akses terminal SSH atau Composer CLI.
  - Memastikan seluruh dependensi PHP (`vendor/`) telah terkompilasi dan terpaket lengkap secara utuh tanpa risiko file korup atau kegagalan transfer file via FTP/File Manager cPanel akibat ribuan file kecil terpisah.
- **File Terkait:**
  - [`vendor.zip`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/vendor.zip) (Arsip berukuran ~43 MB berisi folder `vendor/` lengkap)
- **Tindakan Teknis:**
  - Pembuatan arsip kompresi `vendor.zip` yang mencakup seluruh package Laravel 11, dependencies PHP Composer, autoload files, dan class mapping terverifikasi.
  - Menjadikan proses instalasi di cPanel hosting efisien: pengguna hanya perlu mengunggah 1 file zip dan mengekstraknya langsung melalui cPanel File Manager dalam hitungan detik.
- **Hasil:**
  - Arsip `vendor.zip` siap di-deploy langsung ke hosting production.

---

#### ⏰ Pukul 23:54:00 WIB — Peningkatan Keandalan Symlink Storage (/storage-link) & Rute Fallback Foto cPanel
- **Latar Belakang & Masalah:**
  - Pada lingkungan production cPanel hosting / shared hosting, pembuatan symbolic link (`storage:link`) kerap mengalami kendala atau dibatasi oleh provider hosting (fungsi PHP `symlink()` dinonaktifkan, atau symlink putus saat restrukturisasi root directory ke `public_html`).
  - Hal ini menyebabkan foto profil karyawan (`storage/employees/photos`) dan foto absensi kehadiran (`storage/attendances`) berpotensi gagal dimuat (broken image / HTTP 404).
  - Selain itu, subfolder penyimpanan di dalam `storage/app/public` belum tentu otomatis tersedia atau memiliki izin tulis (*write permission*) yang benar setelah proses migrasi.
- **File Terkait:**
  - [`routes/web.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/routes/web.php)
- **Tindakan Teknis & Solusi:**
  1. **Peningkatan Fungsionalitas Endpoint `/storage-link`:**
     - Menambahkan pengecekan dan pembuatan otomatis direktori target `storage_path('app/public')` dengan permission `0775`.
     - Mengotomatiskan pembuatan subfolder kritis `employees/photos` dan `attendances` dengan permission `0775` jika belum ada.
     - Membersihkan symlink lama jika berstatus broken (`is_link($link) -> @unlink($link)`).
     - Menjalankan `Artisan::call('storage:link')` dengan error handling terstruktur dan mekanisme fallback native `symlink($target, $link)`.
     - Menyediakan audit status ganda: verifikasi koneksi `public/storage` dan pengujian hak akses tulis (`is_writable($target)`).
     - Menampilkan respon visual antarmuka HTML rapi dan modern yang melaporkan seluruh status pemeriksaan serta tombol langsung kembali ke Portal Karyawan.
  2. **Penambahan Rute Fallback Direct-Serving (`Route::get('/storage/{path}')`):**
     - Menambahkan route handling `Route::get('/storage/{path}', ...)->where('path', '.*')`.
     - Jika web server hosting tidak mendukung symlink atau symlink diblokir, setiap permintaan URL file `/storage/...` akan dilayani langsung oleh Laravel via `response()->file($filePath)` dari `storage_path('app/public/' . $path)`.
     - Solusi ini memberikan jaminan 100% foto profil dan foto absensi tetap tampil sempurna di browser pengguna tanpa ketergantungan pada izin symlink hosting.
- **Hasil Verifikasi:**
  - Endpoint `/storage-link` memberikan diagnostik status storage yang jelas dan dapat diakses tim kapan saja.
  - Gambar foto karyawan dan absensi terbukti dapat diakses baik melalui jalur symlink maupun melalui route direct-serving fallback.

---

### 6. Senin, 12 Oktober 2026

#### ⏰ Pukul 00:02:00 WIB — Penonaktifan Opsi Bawaan Serve Storage pada Disk Filesystem (config/filesystems.php)
- **Latar Belakang & Kebutuhan:**
  - Pada konfigurasi bawaan Laravel 11/12, opsi `'serve' => true` pada driver disk `local` dan `public` mengaktifkan fitur internal framework untuk melayani file privat/publik.
  - Untuk mencegah duplikasi alur serving, menghindari potensi konflik dengan rute fallback kustom `/storage/{path}`, serta mengoptimalkan performa web server production/cPanel secara bersih, opsi bawaan tersebut dinonaktifkan secara eksplisit.
- **File Terkait:**
  - [`config/filesystems.php`](file:///home/prtmagum/Documents/REPLAY%20NET%20ID/LOCAL/replay-bos-prod/replay-noc/config/filesystems.php)
- **Tindakan Teknis:**
  - Mengubah `'serve' => true` menjadi `'serve' => false` pada disk `local` (`storage_path('app/private')`).
  - Menambahkan konfigurasi eksplisit `'serve' => false` pada disk `public` (`storage_path('app/public')`).
- **Hasil Verifikasi:**
  - Akses file publik diatur secara penuh dan presisi melalui rute web Laravel terpadu dan symlink statis web server tanpa interferensi middleware bawaan serve disk.
  - Seluruh 48 automated feature & unit test Laravel lulus 100% tanpa regresi.

---

## 📌 Status Sistem Saat Ini

- **Framework Backend:** Laravel 11 (PHP 8.2+)
- **Database:** MariaDB / MySQL
- **Frontend Hybrid:** Blade View + Bootstrap 5 + Tailwind CSS v4 + Vue 3 Component
- **Status Fitur Tiket, Client, Portal, Storage & Resource:**
  - ✅ Pagination 10 data aktif
  - ✅ Format nomor tiket `CP-`, `RS-`, `RH-` aktif
  - ✅ Format nomor layanan client murni angka (`92021...` Reseller, `92022...` Corporate, `92023...` Home) aktif & terselaras rapi
  - ✅ Command Artisan Penyelaras Nomor Layanan (`customers:align-service-numbers`) aktif
  - ✅ Form Surat Penawaran: Input Harga Satuan Readonly & Format Titik Ribuan (`3.000.000`) aktif
  - ✅ Modul Data Karyawan & Portal: Label BPJS Ketenagakerjaan & BPJS Kesehatan seragam aktif
  - ✅ Detail Absensi Mandiri: Foto Selfie Live, Watermark Logo Replay & Timestamp WIB, Alamat & GPS aktif
  - ✅ Portal Karyawan & Portal Reseller aktif
  - ✅ Portal Teknisi Lapangan (`/portal-teknisi`) aktif
  - ✅ Role Baru 'Teknisi' terdaftar di Roles & Permissions (siap dikelola mandiri)
  - ✅ Sidebar Scrolling Vertikal Aktif (seluruh menu bawah mudah diakses)
  - ✅ Modul Pengajuan Upgrade/Downgrade Paket & Approval Finance aktif
  - ✅ Menu Monitoring Resource Database cPanel (`/resources`) aktif
  - ✅ Counter status `HANDLE` dan `CLOSED` aktif
  - ✅ Tombol hapus tiket (trash merah) aktif
  - ✅ Tombol hapus pelanggan aktif
  - ✅ Template WhatsApp auto-fill aktif dan terverifikasi 100% normal
  - ✅ Fitur Import Spreadsheet Pelanggan & Nomor Layanan Otomatis aktif dan terverifikasi 100% normal
  - ✅ Progress Update Perbaikan dengan Lampiran Foto di Timeline aktif dan terverifikasi 100% normal
  - ✅ Tampilan Link Client Mockup Handphone Real-Time aktif dan terverifikasi 100% normal
  - ✅ Link Tracking Client Permanen (Tidak Kadaluarsa) & Sajian Laporan RFO aktif dan terverifikasi 100% normal
  - ✅ Anti-Overflow & Word-Wrap Responsif pada Seluruh Card Teks Sharelink aktif dan terverifikasi 100% normal
  - ✅ Text Alignment Rata Kiri Presisi pada Seluruh Balon Catatan & RFO Timeline aktif dan terverifikasi 100% normal
  - ✅ Fitur Tutup / Kembalikan Sidebar Mobile (Tombol X & Backdrop Overlay Klik-Luar) aktif dan terverifikasi 100% normal
  - ✅ Modal Lightbox Foto Absensi Karyawan Bebas Error JavaScript (Format Koordinat GPS String/Number Aman) aktif
  - ✅ Redesign UI/UX Modal Absensi Check-in & Standardisasi Watermark Resmi Replay (Live Camera Wajib & Tanpa Upload Galeri) aktif
  - ✅ Endpoint Diagnostik & Perbaikan `/storage-link` dengan Auto-Create Subfolder & Status Health Check aktif
  - ✅ Rute Fallback Direct Serving `/storage/{path}` Jaminan Anti-Broken Image di cPanel Hosting aktif
  - ✅ Paket Deployment `vendor.zip` Siap Upload Ekstraksi cPanel Hosting aktif

