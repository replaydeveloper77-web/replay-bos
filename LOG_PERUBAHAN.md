# Log Perubahan dan Update Sistem Replay NOC (Replay BOS)

Dokumen ini mencatat seluruh riwayat pembaruan, perbaikan, dan penyesuaian fitur pada aplikasi **Replay NOC** untuk periode kemarin (**Selasa, 06 Oktober 2026**) dan hari ini (**Rabu, 07 Oktober 2026**).

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

## 📌 Status Sistem Saat Ini

- **Framework Backend:** Laravel 11 (PHP 8.2+)
- **Database:** MariaDB / MySQL
- **Frontend Hybrid:** Blade View + Bootstrap 5 + Tailwind CSS v4 + Vue 3 Component
- **Status Fitur Tiket:**
  - ✅ Pagination 10 data aktif
  - ✅ Format nomor tiket `CP-`, `RS-`, `RH-` aktif
  - ✅ Counter status `HANDLE` dan `CLOSED` aktif
  - ✅ Tombol hapus tiket (trash merah) aktif
  - ✅ Tombol hapus pelanggan aktif
  - ✅ Template WhatsApp auto-fill aktif dan terverifikasi 100% normal
