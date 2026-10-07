<?php

namespace Database\Seeders;

use App\Models\ContractTemplate;
use Illuminate\Database\Seeder;

class ContractTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $pkwt = <<<'HTML'
<p>
    Pada hari ini {{hari}}, tanggal {{tanggal}} ({{tanggal}}) bulan {{bulan}} ({{bulan_angka}}) tahun {{tahun}} ({{tahun}}) telah dibuat dan disepakati perjanjian kerja antara:
</p>

<table class="party-table">
    <tr><td class="party-label">Nama Lengkap</td><td>: {{nama_direktur}}</td></tr>
    <tr><td class="party-label">Alamat</td><td>: {{alamat_perusahaan}}</td></tr>
    <tr><td class="party-label">Jabatan</td><td>: {{jabatan_direktur}}</td></tr>
    <tr><td class="party-label">No. KTP</td><td>: {{ktp_direktur}}</td></tr>
</table>
<p>
    Dalam hal ini bertindak untuk dan atas nama {{nama_perusahaan}} yang selanjutnya di dalam surat perjanjian ini akan disebut sebagai <strong>PIHAK PERTAMA</strong>.
</p>

<table class="party-table">
    <tr><td class="party-label">Nama Lengkap</td><td>: {{nama_karyawan}}</td></tr>
    <tr><td class="party-label">Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
    <tr><td class="party-label">Tempat, Tgl Lahir</td><td>: {{tempat_lahir}}, {{tanggal_lahir}}</td></tr>
    <tr><td class="party-label">Alamat</td><td>: {{alamat_karyawan}}</td></tr>
    <tr><td class="party-label">No. KTP</td><td>: {{ktp_karyawan}}</td></tr>
</table>
<p>
    Dalam hal ini bertindak untuk dan atas nama sendiri. Selanjutnya di dalam surat perjanjian ini akan disebut sebagai <strong>PIHAK KEDUA</strong>.
</p>

<p>
    Kedua belah pihak sepakat untuk mengikatkan diri dalam {{jenis_perjanjian}} dengan ketentuan - ketentuan sebagai berikut :
</p>

<div class="pasal-title">PASAL 1</div>
<p>PIHAK PERTAMA menerima dan mempekerjakan PIHAK KEDUA sebagai:</p>
<ul>
    <li>Status : {{status_karyawan}}</li>
    <li>Jabatan : {{jabatan_karyawan}}</li>
</ul>

<div class="pasal-title">PASAL 2</div>
<ol>
    <li>PIHAK KEDUA bersedia menerima dan melaksanakan tugas dan tanggungjawab dalam pekerjaan tersebut serta tugas-tugas lain yang diberikan PIHAK PERTAMA dengan sebaik-baiknya.</li>
    <li>PIHAK KEDUA bersedia tunduk dan melaksanakan seluruh ketentuan yang telah diatur baik dalam Buku Saku Peraturan dan Tata Tertib maupun ketentuan lain yang menjadi keputusan Direksi dan Manajemen Perusahaan.</li>
    <li>PIHAK KEDUA bersedia menyimpan dan menjaga kerahasiaan baik dokumen maupun informasi milik PIHAK PERTAMA dan tidak dibenarkan memberikan dokumen atau informasi baik secara lisan maupun tertulis kepada pihak lain.</li>
    <li>PIHAK KEDUA bersedia ditempatkan dimana saja apabila sewaktu-waktu ditugaskan oleh Perusahaan.</li>
    <li>Waktu kerja PIHAK KEDUA adalah lepas hari Senin – Jumat mulai dari Pukul 08.00 WIB s/d Pukul 17.00 WIB.</li>
    <li>PIHAK KEDUA bertanggung jawab penuh terhadap peralatan kerja PIHAK PERTAMA dan menjaganya sebaik mungkin.</li>
</ol>

<div class="pasal-title">PASAL 3</div>
<p>Selama {{sebutan_masa}} berlangsung PIHAK PERTAMA dapat memutuskan hubungan kerja dengan PIHAK KEDUA secara sepihak apabila ternyata:</p>
<ol>
    <li>PIHAK KEDUA melakukan pelanggaran dari ketentuan Pasal 2 Surat Perjanjian Kerja ini setelah sebelumnya mendapat teguran dan peringatan secara patut sesuai dengan prosedur dan Buku Saku Peraturan Perusahaan.</li>
    <li>PIHAK KEDUA tidak dapat menjalankan tugas, target atau sasaran kerja yang telah ditetapkan oleh PIHAK PERTAMA.</li>
    <li>PIHAK KEDUA terlibat baik secara langsung maupun tidak langsung dalam tindak pidana dan atau Hukum Perdata di Republik Indonesia.</li>
    <li>PIHAK PERTAMA dalam hal ini Perusahaan berada dalam situasi dan kondisi yang tidak memungkinkan lagi untuk mempekerjakan PIHAK KEDUA akibat memburuknya kinerja Perusahaan.</li>
</ol>

<div class="pasal-title">PASAL 4</div>
<ol>
    <li>PIHAK KEDUA berhak atas {{gaji_label}} dari pekerjaan yang dilakukannya, dari PIHAK PERTAMA dengan Jumlah Rp. {{gaji_jumlah}} yang akan dibayarkan pada akhir bulan di setiap bulannya.</li>
</ol>

<div class="pasal-title">PASAL 5</div>
<ol>
    <li>PIHAK PERTAMA wajib membayarkan {{gaji_label}} kepada PIHAK KEDUA sebagaimana tersebut pada Pasal 4 yang dilaksanakan setiap bulan sesuai dengan ketentuan Perusahaan dengan tidak mengesampingkan kondisi-kondisi tertentu yang mungkin terjadi dimana PIHAK PERTAMA membutuhkan kerja sama dan kesadaran PIHAK KEDUA demi kesinambungan perusahaan.</li>
</ol>

<div class="pasal-title">PASAL 6</div>
<ol>
    <li>Surat Perjanjian Kerja ini berlaku sejak tanggal {{tanggal_mulai}} hingga tanggal {{tanggal_berakhir}}.</li>
    <li>Surat Perjanjian Kerja ini dapat dibatalkan dan atau menjadi tidak berlaku antara lain karena:
        <ol type="a">
            <li>Jangka waktu yang diperjanjikan sebagaimana tersebut dalam ayat 1 telah berakhir.</li>
            <li>Diakhiri oleh kedua belah pihak (kesepakatan kedua belah pihak) walaupun jangka waktu belum berakhir.</li>
            <li>Dilakukannya Pemutusan Hubungan Kerja oleh PIHAK PERTAMA karena hal-hal yang sebagaimana diatur dalam Pasal 3 Surat Perjanjian ini.</li>
        </ol>
    </li>
    <li>PIHAK PERTAMA tidak berkewajiban untuk memberikan uang pesangon, uang jasa, atau ganti kerugian apapun kepada PIHAK KEDUA setelah berakhirnya masa kerja waktu tertentu ({{sebutan_jenis}}).</li>
    <li>Surat Perjanjian Kerja ini dapat diperpanjang atas kesepakatan kedua belah pihak, pada saat berakhirnya masa kerja waktu tertentu sesuai ayat 1.</li>
</ol>

<div class="pasal-title">PASAL 7</div>
<ol>
    <li>Surat Perjanjian Kerja untuk Waktu Tertentu ini dibuat dan ditandatangani oleh kedua belah pihak dengan tanpa ada pengaruh dana atau paksaan dari siapapun serta mengikat kedua belah pihak untuk mentaati dan melaksanakannya dengan penuh tanggungjawab.</li>
    <li>Apabila di kemudian hari Surat Perjanjian Kerja ini ternyata masih terdapat hal-hal yang bertentangan dengan peraturan perundang-undangan ketenagakerjaan di Republik Indonesia dan/atau perkembangan Peraturan Perusahaan, maka akan diadakan peninjauan serta penyesuaian atas persetujuan kedua belah pihak.</li>
    <li>Surat Perjanjian ini dibuat dalam rangkap 2 (dua) yang memiliki kekuatan hukum yang sama dan dipegang oleh masing-masing pihak.</li>
</ol>
HTML;

        $pkwtt = <<<'HTML'
<p>Yang bertanda tangan di bawah ini :</p>

<table class="party-table">
    <tr><td class="party-label">1. Nama Lengkap</td><td>: {{nama_direktur}}</td></tr>
    <tr><td class="party-label">Alamat</td><td>: {{alamat_perusahaan}}</td></tr>
    <tr><td class="party-label">Jabatan</td><td>: {{jabatan_direktur}}</td></tr>
    <tr><td class="party-label">No. KTP</td><td>: {{ktp_direktur}}</td></tr>
</table>
<p>Dalam hal ini bertindak untuk dan atas nama :</p>
<table class="party-table">
    <tr><td class="party-label">Perusahaan</td><td>: {{nama_perusahaan}}</td></tr>
    <tr><td class="party-label">Alamat</td><td>: {{alamat_perusahaan}}</td></tr>
    <tr><td class="party-label">Bidang usaha</td><td>: {{bidang_usaha}}</td></tr>
</table>
<p>Selanjutnya di dalam surat perjanjian ini akan disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>

<table class="party-table">
    <tr><td class="party-label">2. Nama Lengkap</td><td>: {{nama_karyawan}}</td></tr>
    <tr><td class="party-label">Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>
    <tr><td class="party-label">Tempat/Tgl Lahir</td><td>: {{tempat_lahir}}, {{tanggal_lahir}}</td></tr>
    <tr><td class="party-label">Alamat</td><td>: {{alamat_karyawan}}</td></tr>
    <tr><td class="party-label">No. KTP</td><td>: {{ktp_karyawan}}</td></tr>
</table>
<p>Dalam hal ini bertindak untuk dan atas nama sendiri. Selanjutnya di dalam surat perjanjian ini akan disebut sebagai <strong>PIHAK KEDUA</strong>.</p>

<p>Kedua belah pihak sepakat untuk membuat perjanjian kerja dengan ketentuan dan syarat-syarat sebagai berikut :</p>

<div class="pasal-title">PASAL 1 : KETENTUAN UMUM</div>
<ol>
    <li>Perusahaan ({{nama_perusahaan}}) adalah milik PIHAK PERTAMA dan PIHAK PERTAMA mempunyai kuasa penuh untuk menetapkan kebijakan dan peraturan di dalam Perusahaan ({{nama_perusahaan}}).</li>
    <li>PIHAK PERTAMA dengan ini menyatakan menerima PIHAK KEDUA sebagai Karyawan/Pekerja Waktu Penuh Perusahaan ({{nama_perusahaan}}) yang terletak di {{alamat_perusahaan}} dalam bidang usaha {{bidang_usaha}}.</li>
    <li>PIHAK KEDUA dengan ini menyatakan bersedia menjadi karyawan waktu penuh PIHAK PERTAMA dalam posisi jabatan kerja ({{jabatan_karyawan}}) yang cakupan kerjanya diterangkan pada Pasal 5.</li>
    <li>PIHAK PERTAMA dan PIHAK KEDUA bersedia mentaati surat perjanjian ini. Dan PIHAK KEDUA bersedia menaati tata tertib dan peraturan yang telah ditetapkan perusahaan ({{nama_perusahaan}}).</li>
</ol>

<div class="pasal-title">PASAL 2 : WAKTU BERLAKU</div>
<p>Perjanjian ini mulai berlaku sejak tanggal {{tanggal_mulai}}, PIHAK KEDUA ditetapkan sebagai Karyawan Tetap Perusahaan {{nama_perusahaan}}.</p>

<div class="pasal-title">PASAL 3 : HAK</div>
<p>1. Hak-hak yang didapat oleh PIHAK PERTAMA adalah sebagai berikut :</p>
<ol type="a">
    <li>Mendapat kontribusi dari PIHAK KEDUA berupa hasil dari pekerjaan sesuai dengan posisi kerja yang PIHAK KEDUA dapat pada Pasal 1 ayat (2).</li>
    <li>Membuat ketetapan, peraturan, dan kebijakan perusahaan [{{nama_perusahaan}}].</li>
    <li>Mengawasi, mengkoordinir, menegur, dan memberhentikan PIHAK KEDUA.</li>
    <li>Memindahkan, menaikkan, atau menurunkan posisi dan jabatan kerja PIHAK KEDUA. Dalam hal apabila terjadi, penyesuaian isi surat perjanjian kerja ini akan diatur kemudian di dalam Adendum.</li>
    <li>Meningkatkan nilai upah, tunjangan, dan atau bonus yang terdapat pada Pasal 7.</li>
    <li>Memotong atau menurunkan nilai upah, tunjangan, dan atau bonus yang terdapat pada Pasal 7 apabila menemukan PIHAK KEDUA tidak memenuhi peraturan perusahaan [{{nama_perusahaan}}] yang ditetapkan PIHAK PERTAMA.</li>
</ol>
<p>2. Hak-hak yang didapat oleh PIHAK KEDUA adalah sebagai berikut :</p>
<ol type="a">
    <li>Mendapat upah, tunjangan, dan atau bonus dari PIHAK PERTAMA sesuai Pasal 7.</li>
    <li>Mendapatkan perlakuan yang baik dan sesuai di dalam pekerjaan.</li>
    <li>Mendapatkan dan atau menggunakan fasilitas dan pelatihan yang disediakan PIHAK PERTAMA.</li>
    <li>Mendapatkan hak waktu kerja yang disebut pada Pasal 6 ayat (2).</li>
</ol>

<div class="pasal-title">PASAL 4 : KEWAJIBAN</div>
<p>1. Kewajiban dan kewenangan PIHAK PERTAMA adalah sebagai berikut :</p>
<ol type="a">
    <li>Memberikan hak-hak PIHAK KEDUA secara penuh.</li>
    <li>Memberikan arahan dan putusan sesuai lingkup pekerjaan kepada PIHAK KEDUA.</li>
    <li>Menjaga nama baik perusahaan [{{nama_perusahaan}}].</li>
</ol>
<p>2. Kewajiban PIHAK KEDUA adalah sebagai berikut :</p>
<ol type="a">
    <li>Memahami dan melaksanakan visi dan misi perusahaan [{{nama_perusahaan}}] secara penuh dan bertanggung jawab.</li>
    <li>Bekerja dan melaksanakan tugas sesuai posisi kerja yang telah ditetapkan secara penuh dan bertanggung jawab.</li>
    <li>Memenuhi waktu kerja.</li>
    <li>Mengikuti program-program dan pelatihan-pelatihan yang diselenggarakan perusahaan [{{nama_perusahaan}}].</li>
    <li>Menjaga nama baik perusahaan [{{nama_perusahaan}}].</li>
    <li>Menjaga dan merawat aset, fasilitas, dan kerahasiaan data-data perusahaan [{{nama_perusahaan}}].</li>
    <li>Menghormati dan melaksanakan nilai-nilai yang telah ditetapkan perusahaan [{{nama_perusahaan}}].</li>
</ol>

<div class="pasal-title">PASAL 5 : CAKUPAN KERJA</div>
<ol>
    <li>Cakupan kerja PIHAK KEDUA adalah melaksanakan berbagai kegiatan operasional di dalam perusahaan [{{nama_perusahaan}}] yang ditugaskan oleh PIHAK PERTAMA, dengan Job Desc sebagai berikut :
        <ol type="a">
            <li>........................................</li>
            <li>........................................</li>
            <li>........................................</li>
            <li>........................................</li>
            <li>........................................</li>
        </ol>
    </li>
    <li>Ikut membantu melakukan kegiatan lain di dalam perusahaan [{{nama_perusahaan}}] saat diperlukan, dan ikut membantu kegiatan perusahaan mitra [{{nama_perusahaan}}] saat diperlukan.</li>
</ol>

<div class="pasal-title">PASAL 6 : WAKTU KERJA</div>
<ol>
    <li>PIHAK KEDUA wajib memenuhi waktu kerja 8 (delapan) jam kerja sehari dalam 5 (lima) hari kerja setiap minggunya atau 40 (empat puluh) jam kerja selama seminggu di luar jam istirahat.</li>
    <li>Waktu kerja adalah sesuai dengan yang ditetapkan oleh PIHAK PERTAMA di luar perjanjian kerja ini.</li>
    <li>PIHAK KEDUA berhak mendapatkan :
        <ol type="a">
            <li>Waktu istirahat sebanyak 1 (satu) jam pada 1 (satu) hari kerja sesuai waktu kerja.</li>
            <li>Waktu istirahat sebanyak 2 (dua) hari setelah 5 (lima) hari bekerja sesuai waktu kerja.</li>
            <li>Cuti sebanyak 1 (satu) hari setelah 1 (satu) bulan bekerja sesuai waktu kerja dan hak ini tidak dapat diakumulasikan dengan hari setelahnya setelah 1 (satu) bulan tersebut berlalu.</li>
            <li>Izin libur bekerja di luar cuti setelah mendapat persetujuan dan konsekuensi yang disetujui oleh pihak berwenang di perusahaan [{{nama_perusahaan}}].</li>
        </ol>
    </li>
</ol>

<div class="pasal-title">PASAL 7 : UPAH &amp; TUNJANGAN</div>
<ol>
    <li>PIHAK KEDUA berhak mendapatkan upah pokok dari PIHAK PERTAMA sebesar Rp. {{gaji_jumlah}},- setiap bulannya.</li>
    <li>PIHAK KEDUA berhak mendapatkan tunjangan diluar upah pokok sebagai berikut :
        <ol type="a">
            <li>Tunjangan komunikasi, sebesar Rp. 50.000,- /bulan</li>
            <li>Tunjangan uang makan, sebesar Rp. 10.000,- /hari</li>
            <li>Tunjangan Piket Hari Libur Nasional, Hari Sabtu dan Hari Minggu, sebesar Rp. 50.000,- /Piket</li>
            <li>Tunjangan Akomodasi Transport diluar Zona Penempatan, sebesar Rp. 25.000,- /Jalan</li>
        </ol>
    </li>
    <li>PIHAK KEDUA berhak mendapatkan bonus dan komisi di luar upah pokok sebagai berikut :
        <ol type="a">
            <li>Bonus Tidak Ambil Cuti, sebesar upah 1 hari kerja di bulan bersangkutan.</li>
            <li>Bonus Lain, besarannya tergantung terhadap prestasi yang dilakukan, sesuai dengan kebijakan yang ditentukan perusahaan [{{nama_perusahaan}}].</li>
        </ol>
    </li>
    <li>Apabila PIHAK KEDUA tidak memenuhi waktu kerja sesuai dengan Pasal 6 maka upah, bonus, dan tunjangan akan diupayakan untuk dihitung seadil-adilnya oleh PIHAK PERTAMA.</li>
    <li>PIHAK KEDUA bersedia untuk membayar sejumlah dana yang telah disepakati oleh para karyawan sebagai dana talangan umat untuk kepentingan para karyawan sendiri, dan bersedia ditarik dan dikelola setiap bulan oleh PIHAK PERTAMA. Adapun apabila ada, maka PIHAK KEDUA bersedia untuk membayar sejumlah dana Tabarru atau jaminan sosial yang pengelolaannya ditunjuk oleh PIHAK PERTAMA demi keuntungan PIHAK KEDUA sendiri.</li>
    <li>Dalam hal terjadi peningkatan Upah Pokok dan atau Tunjangan yang dilakukan oleh PIHAK PERTAMA, PIHAK PERTAMA dan PIHAK KEDUA sepakat untuk tidak menerbitkan Adendum kecuali terjadi kejadian yang memaksa kedua belah pihak untuk menerbitkannya.</li>
</ol>

<div class="pasal-title">PASAL 8 : PEMBERHENTIAN PERJANJIAN</div>
<p>Pemberhentian perjanjian ini dapat terjadi apabila :</p>
<ol>
    <li>PIHAK PERTAMA secara sepihak memberhentikan PIHAK KEDUA dikarenakan:
        <ol type="a">
            <li>PIHAK KEDUA dinilai tidak dapat memenuhi persyaratan kerja yang telah ditentukan oleh perusahaan [{{nama_perusahaan}}].</li>
            <li>Kebijakan yang diambil demi kepentingan perusahaan [{{nama_perusahaan}}].</li>
        </ol>
    </li>
    <li>PIHAK KEDUA melakukan pengunduran diri dengan ketentuan:
        <ol type="a">
            <li>PIHAK KEDUA telah melewati masa kerja selama 1 (satu) tahun dengan menyertakan surat pengajuan pengunduran diri secara tertulis kepada PIHAK PERTAMA selambat-lambatnya 2 (dua) bulan sebelum pengunduran dirinya, kecuali tidak disyaratkan oleh PIHAK PERTAMA di luar perjanjian ini.</li>
            <li>PIHAK KEDUA bersedia untuk bertanggung jawab dalam mencari karyawan baru yang akan menggantikan dirinya sebelum pengunduran dirinya dilakukan, kecuali tidak disyaratkan oleh PIHAK PERTAMA secara terpisah di luar perjanjian ini. Karyawan baru haruslah orang yang memenuhi persyaratan yang ditetapkan oleh perusahaan [{{nama_perusahaan}}].</li>
            <li>PIHAK KEDUA bersedia untuk memberi pelatihan kepada karyawan baru yang menggantikan dirinya paling sedikit selama 1 (satu) bulan sebelum pengunduran dirinya terjadi, kecuali tidak disyaratkan oleh PIHAK PERTAMA di luar perjanjian ini.</li>
        </ol>
    </li>
</ol>

<div class="pasal-title">PASAL 9 : KELALAIAN</div>
<p>Apabila ditemukan kelalaian oleh PIHAK KEDUA, maka PIHAK PERTAMA berhak memberikan teguran baik tertulis atau lisan. Pada teguran yang ketiga PIHAK PERTAMA berhak untuk mengikutsertakan surat penalti atau hukuman dan atau surat perintah pemberhentian kerja kepada PIHAK KEDUA.</p>

<div class="pasal-title">PASAL 10 : PERUBAHAN</div>
<p>Perubahan isi surat perjanjian dapat dilakukan apabila PIHAK PERTAMA dan PIHAK KEDUA menemui kesepakatan bersama untuk mengubah isinya. Perubahan isi surat perjanjian ini diatur kemudian dalam bentuk Adendum yang harus ditandatangani kedua belah pihak pada surat perjanjian tertulis yang bermaterai.</p>

<div class="pasal-title">PASAL 11 : PERSELISIHAN</div>
<p>Segala perselisihan yang timbul akibat surat perjanjian dan atau ketika masa perjanjian berlaku, akan diselesaikan secara musyawarah untuk mencapai mufakat. Apabila tidak dapat diselesaikan secara musyawarah, maka kedua belah pihak akan menyelesaikannya melalui peraturan hukum yang berlaku.</p>

<div class="pasal-title">PASAL 12 : FORCE MAJEUR</div>
<p>Apabila terjadi kejadian di luar kuasa kedua belah pihak seperti perang, penyerangan, kerusuhan, kriminalitas, atau bencana alam seperti gempa bumi, banjir, gunung meletus, dan bencana alam lainnya yang mengakibatkan perubahan besar pada efektifitas surat perjanjian. Maka hal-hal tersebut dapat menghilangkan kewajiban dan liabilitas PIHAK PERTAMA dan PIHAK KEDUA terhadap perjanjian ini.</p>

<p>
    Demikian Surat Perjanjian Kerja [{{nama_perusahaan}}] ini dibuat, setelah kedua belah pihak membaca dan memahami isinya. Kemudian dengan sukarela tanpa paksaan atau tekanan dari siapapun bersama-sama menandatanganinya di atas materai Rp. 10.000,-.
</p>

<p style="text-align: center; margin-top: 16px;">
    Dibuat di : Kuningan<br>
    Hari / Tanggal : {{tanggal_hari_ini_lengkap}}
</p>
HTML;

        // firstOrCreate, not updateOrCreate — once HR has customized a template's
        // redaksi, re-running this seeder (e.g. a future full db:seed) must never
        // clobber their edits back to this default content.
        ContractTemplate::firstOrCreate(
            ['type' => 'PKWT'],
            ['title' => 'Perjanjian Kerja Waktu Tertentu (Kontrak/Magang/Masa Percobaan)', 'body' => $pkwt],
        );

        ContractTemplate::firstOrCreate(
            ['type' => 'PKWTT'],
            ['title' => 'Perjanjian Kerja Waktu Tidak Tertentu (Karyawan Tetap)', 'body' => $pkwtt],
        );
    }
}
