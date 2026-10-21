================================================================================
DOKUMEN SPESIFIKASI PROYEK: SIGAP PASAR
================================================================================

0. IDENTITAS INSTANSI & BRANDING RESMI (WAJIB DIPATUHI)
--------------------------------------------------------------------------------
- Instansi Pemilik : Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat (Diskoperindag Prov. Sulbar)
- Wilayah Cakupan : 6 Kabupaten se-Sulawesi Barat (Mamuju, Majene, Polewali Mandar, Mamasa, Pasangkayu, Mamuju Tengah)
- File Logo Resmi  : public/images/logo.png (dan public/logo.png)
- WARNA UTAMA PROYEK (MANDATORY PRIMARY COLOR):
  * Kode Warna Utama : #2e7d32 (Forest Green Resmi Diskoperindag Sulbar)
  * Dark Shade       : #1b5e20 (Dark Forest)
  * Accent/Tint      : #4caf50 & #e8f5e9 (Soft Mint / Light Surface)
  * PERINGATAN PENTING: Warna dengan kode #2e7d32 ini WAJIB selalu diingat dan digunakan sebagai warna utama/primer di seluruh antarmuka web, navigasi, tombol primer, header, badge, diagram, dan aksen visual proyek ini tanpa pengecualian!

1. DESKRIPSI UMUM
--------------------------------------------------------------------------------
"Sigap Pasar" adalah sebuah sistem informasi berbasis web yang dirancang untuk Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat dalam mendeteksi, menindaklanjuti, dan menyelesaikan isu-isu terkait pasar (seperti lonjakan harga, kelangkaan stok, atau praktik curang) yang viral di media sosial. Sistem ini menjembatani laporan siber dengan tindakan nyata di lapangan (operasi pasar/intervensi) melalui rantai komando yang terstruktur di seluruh wilayah Sulawesi Barat.

2. AKTOR DAN HAK AKSES (ROLE-BASED ACCESS CONTROL)
--------------------------------------------------------------------------------
Sistem ini melibatkan beberapa peran pengguna:
- Pengawas Siber   : Bertugas memantau media sosial dan menginput data isu baru.
- Admin            : Bertugas menugaskan petugas lapangan, menginput data intervensi, dan menutup isu.
- Petugas Lapangan : Bertugas mengecek langsung ke lokasi pasar dan melaporkan kondisi riil.
- Super Admin      : Mengelola data master (lokasi, komoditas) dan akun pengguna.
- Pimpinan         : Memantau ringkasan eksekutif, peta sebaran isu wilayah, dan tren perkembangan harga komoditas.

3. ALUR KERJA SISTEM (WORKFLOW)
--------------------------------------------------------------------------------
Tahap 1: Deteksi dan Input Isu (Oleh Pengawas Siber)
- Pengawas Siber mendeteksi isu viral dan menginput data ke dalam sistem.
- Data yang diinput: Tanggal & waktu, platform sumber, link postingan, nama akun, jumlah interaksi/mention (virality), kutipan postingan, jenis komoditas, kategori isu, lokasi spesifik (pasar & kabupaten), serta bukti screenshot.

Tahap 2: Penugasan & Pemilihan Isu Lapangan (Oleh Petugas Lapangan / Admin)
- Laporan isu baru langsung masuk ke antrian laporan operasional dan siap dipilih oleh Petugas Lapangan.
- Petugas Lapangan dapat langsung memilih/mengambil sendiri isu pasar di wilayahnya, atau Admin dapat menugaskan petugas spesifik secara terpadu.
- Sistem mengirimkan notifikasi dan mencatat riwayat penugasan ke dalam audit trail.

Tahap 3: Cek Fisik & Laporan Lapangan (Oleh Petugas Lapangan)
- Petugas menuju lokasi dan melakukan verifikasi faktual lapangan.
- Data yang diinput: 
  * Bukti foto terbaru (dilengkapi timestamp dan watermark waktu).
  * Koordinat GPS (Check-in lokasi).
  * Harga aktual di lapangan untuk perbandingan.
  * Catatan hasil wawancara singkat atau temuan kondisi.

Tahap 4: Intervensi dan Tindak Lanjut (Oleh Admin & Instansi)
- Jika temuan lapangan valid dan butuh tindakan, masuk ke tahap koordinasi (Dinas/Bulog).
- Admin menginput data intervensi:
  * Jenis intervensi (contoh: Operasi Pasar, Sidak Gudang).
  * Status intervensi (Sedang berjalan / Selesai).
  * Deskripsi intervensi dan hasil yang diharapkan.
  * Tanggal mulai dan target selesai.
  * Nama Penanggung Jawab (PIC) dan instansi yang terlibat.
- Admin dapat melakukan "Update Progress" secara berkala.

Tahap 5: Hasil Akhir & Penutupan Isu (Oleh Admin)
- Setelah intervensi selesai, Admin mencatat hasil akhirnya.
- Data yang diinput: 
  * Harga sebelum intervensi vs harga setelah intervensi.
  * Kesimpulan dan catatan akhir.
  * Status akhir isu diubah menjadi "Selesai/Ditutup".

4. FITUR PENDUKUNG UTAMA
--------------------------------------------------------------------------------
- Log / Audit Trail: Mencatat seluruh riwayat aktivitas secara otomatis (Siapa, Melakukan Apa, Kapan) pada setiap tahap.
- Email Notification: Notifikasi otomatis saat penugasan petugas lapangan.
- Dashboard Analitik: Menampilkan statistik isu berdasarkan komoditas, wilayah, tingkat virality, dan performa waktu penanganan (SLA).

5. STRUKTUR DATABASE (GAMBARAN UMUM)
--------------------------------------------------------------------------------
- users               : Menyimpan data kredensial dan role semua pengguna.
- master_data         : Tabel referensi untuk komoditas dan lokasi/kabupaten.
- issues              : Tabel utama penyimpan laporan (link, interaksi, dsb).
- field_assignments   : Menyimpan data penugasan, hasil dari lapangan, dan GPS.
- interventions       : Menyimpan data langkah operasi pasar/koordinasi.
- resolutions         : Menyimpan harga akhir dan catatan penutupan.
- evidences           : Tabel penyimpanan file bukti (foto, screenshot).
- issue_logs          : Menyimpan riwayat perjalanan isu dari awal hingga akhir.

6. REKOMENDASI LANDING PAGE (HALAMAN DEPAN "/")
--------------------------------------------------------------------------------
6.1 Tugas Utama Landing Page
Sigap Pasar bukan produk yang dijual, melainkan sistem pemerintah. Maka landing
page berfungsi sebagai gerbang resmi instansi dengan tiga tugas:
  a. Mengarahkan petugas (Pengawas Siber, Admin, Petugas Lapangan, Super Admin,
     Pimpinan) masuk ke sistem secepat mungkin.
  b. Menjelaskan secara transparan bagaimana satu isu pasar ditangani, dari
     postingan viral sampai harga kembali normal.
  c. Menunjukkan kinerja penanganan lewat statistik agregat (tanpa data sensitif).

Pengunjung: petugas dinas, instansi mitra (Bulog, dinas kabupaten), dan publik
yang ingin tahu bagaimana pemerintah merespons isu pasar.

6.2 Rencana Desain (Token)
Warna (tema terang; hijau resmi mendominasi, tidak ada gradien dekoratif):
  - Hutan Sulbar  #2e7d32  PRIMER: tombol utama, header, badge, garis alur
  - Hutan Gelap   #1b5e20  hover/aktif tombol, teks tautan, footer
  - Daun          #4caf50  aksen kecil: ikon selesai, indikator progres
  - Embun         #e8f5e9  permukaan terang: panel, latar baris selang-seling
  - Kertas        #f7faf7  latar halaman
  - Tinta         #1c2b21  teks utama (hijau sangat gelap, bukan hitam polos)
  Warna fungsional (HANYA untuk badge prioritas, sama dengan dashboard):
  Kritis #c62828, Tinggi #ef6c00, Sedang #f9a825, Rendah #4caf50.

Tipografi (dua keluarga yang jelas berbeda):
  - Judul : Bricolage Grotesque (600-700), tracking sedikit rapat
  - Isi   : Public Sans (400/500/600), line-height 1.6
  - Skala : 14 / 16 / 18 / 24 / 32 / 48 / 64 px; panjang baris maks 70 karakter
  - Angka harga dan statistik memakai tabular-nums agar rata di tabel.

Tata letak: rata kiri di semua section (kesan dokumen resmi, mudah dipindai),
lebar konten maks 1200px, grid 12 kolom, jarak antar section konsisten.

Prinsip khas halaman ini:
  1. Satu elemen yang diingat: "Jalur Penanganan" di hero, yaitu kartu contoh
     kasus yang menampilkan perjalanan isu dari postingan viral sampai selesai.
  2. Sisanya tenang: teks, tabel, daftar. Tidak ada kartu identik berulang,
     tidak ada bayangan abu-abu seragam, tidak ada gradien.
  3. Penomoran hanya dipakai pada Alur 5 Tahap karena itu memang urutan.
  4. Isi diambil dari dunia pasar Sulbar: komoditas, harga per kg, nama pasar,
     enam kabupaten. Bukan ilustrasi generik.

6.3 Struktur Section
  [Header]  Logo (public/images/logo.png) + "Sigap Pasar" + "Diskoperindag
            Prov. Sulawesi Barat" | Alur | Peran | Wilayah | [Masuk]

  [1. Hero]
  +--------------------------------------+------------------------------------+
  | Isu pasar yang viral,                |  Contoh kasus (ilustrasi)          |
  | ditangani sampai tuntas.             |  "Harga cabai rawit naik di        |
  |                                      |   Pasar Mamuju"  Facebook, 2.400   |
  | Sigap Pasar menghubungkan pantauan   |   interaksi                        |
  | media sosial dengan pengecekan       |  o Dilaporkan   (Pengawas Siber)   |
  | langsung di pasar di enam kabupaten  |  o Ditugaskan   (Admin)            |
  | Sulawesi Barat.                      |  o Dicek lapangan (Petugas)        |
  |                                      |  o Operasi pasar (Admin + Bulog)   |
  | [Masuk ke sistem] Lihat alur         |  * Selesai: Rp85.000 -> Rp62.000   |
  +--------------------------------------+------------------------------------+
  Gerak: satu-satunya animasi otomatis. Saat halaman dimuat, garis hijau
  mengisi dari tahap pertama ke terakhir (sekitar 2,5 detik, sekali saja).
  Kartu diberi label "Contoh ilustrasi" agar tidak dikira data nyata.

  [2. Ringkasan penanganan]  Satu baris tabel sederhana, bukan kartu besar:
  Isu masuk bulan ini | Sudah selesai | Rata-rata waktu penanganan | Isu aktif
  Sumber: endpoint agregat dari tabel issues dan issue_logs. Jika belum ada
  data, tampilkan "Belum ada data bulan ini", bukan angka karangan.

  [3. Alur penanganan]  Timeline vertikal 5 tahap (urutan nyata), tiap tahap
  menyebut: siapa pelakunya, apa yang dikerjakan, apa hasilnya.
  Tahap 1 Deteksi isu -> 2 Penugasan lapangan -> 3 Cek fisik -> 4 Intervensi
  -> 5 Penutupan isu.

  [4. Peran dan akses]  Tabel dua kolom (Peran | Tugas dan yang dilihat setelah
  masuk) untuk 5 peran. Bukan kartu.

  [5. Wilayah]  Enam kabupaten (Mamuju, Majene, Polewali Mandar, Mamasa,
  Pasangkayu, Mamuju Tengah) sebagai daftar dengan jumlah isu aktif tiap
  wilayah. Peta sederhana opsional; daftar tetap harus ada untuk aksesibilitas.

  [6. Jejak audit]  Satu panel Embun, teks pendek: setiap tindakan tercatat
  (siapa, melakukan apa, kapan), dan petugas lapangan menerima email penugasan.

  [7. Footer]  Logo, nama lengkap instansi, alamat dan kontak dinas (isi
  dari pihak dinas), tautan Masuk. Latar #1b5e20, teks terang.

6.4 Contoh Teks (bahasa Indonesia, kalimat biasa, kata kerja aktif)
  - Tombol utama : "Masuk ke sistem"   | Tombol kedua: "Lihat alur penanganan"
  - Judul alur   : "Dari postingan viral sampai harga kembali normal"
  - Judul peran  : "Siapa mengerjakan apa"
  - Judul wilayah: "Enam kabupaten, satu sistem"
  - Judul audit  : "Setiap langkah tercatat"
  - Data kosong  : "Belum ada isu aktif di Majene. Isu baru akan muncul di sini."
  Gunakan satu istilah yang sama di seluruh web: "isu", "verifikasi",
  "penugasan", "intervensi", "penutupan". Jangan ganti sinonim.

6.5 Aturan Keselarasan dengan Web Utama
  - Definisikan warna sebagai CSS variable yang SAMA dengan dashboard
    (--color-primary: #2e7d32; --color-primary-dark: #1b5e20;
     --color-accent: #4caf50; --color-surface: #e8f5e9).
  - Pakai komponen tombol, badge prioritas, dan radius yang sama dengan
    dashboard. Satu radius dasar (8px) untuk kontrol; panel besar boleh 12px.
  - Font Bricolage Grotesque + Public Sans juga dipakai di dashboard agar
    perpindahan dari landing ke login terasa satu produk.
  - Tombol "Masuk" mengarah ke halaman login yang sudah ada; landing page tidak
    mengubah logika autentikasi maupun RBAC.

6.6 Yang Harus Dihindari
  - Latar krem dengan aksen terakota, atau latar hitam dengan hijau neon.
  - Satu kata di judul diberi warna/miring sebagai "aksen".
  - Label huruf kapital berjarak di atas setiap judul; panah "->" di semua tombol.
  - Deretan kartu identik dengan bayangan sama; gradien sebagai hiasan.
  - Animasi muncul-geser-naik di setiap section dan hover pada semua kartu.
  - Foto stok generik, angka statistik karangan, testimoni palsu.

6.7 Standar Kualitas Minimum
  - Responsif dari 360px; tabel menggulir di dalam wadahnya sendiri.
  - Kontras teks minimal WCAG AA (teks putih di #2e7d32 lolos AA untuk teks tebal
    atau besar; untuk teks kecil pakai #1b5e20).
  - Fokus keyboard terlihat jelas; tautan "Lewati ke konten utama".
  - Hormati prefers-reduced-motion: tampilkan jalur penanganan dalam kondisi akhir.
  - Logo memakai alt "Logo Diskoperindag Provinsi Sulawesi Barat"; gambar
    dioptimalkan, lcp di bawah 2,5 detik pada jaringan seluler.

7. PROMPT SIAP PAKAI UNTUK MEMBUAT LANDING PAGE
--------------------------------------------------------------------------------
Salin seluruh blok di bawah ke AI coding assistant (Claude Code, Cursor, dll).

--- MULAI PROMPT ---
Peran: Kamu adalah desainer frontend utama untuk "Sigap Pasar", sistem informasi
Diskoperindag Provinsi Sulawesi Barat yang mendeteksi, menindaklanjuti,
dan menutup isu pasar yang viral di media sosial (lonjakan
harga, kelangkaan stok, praktik curang) di 6 kabupaten Sulawesi Barat.

Tugas: Buat landing page untuk route "/" yang menjadi gerbang resmi sistem.
Baca bagian 0 sampai 6 dokumen ini sebagai sumber kebenaran. Jangan mengubah
logika autentikasi, RBAC, database, atau halaman dashboard yang sudah ada.

Tujuan halaman: (1) mengarahkan petugas masuk ke sistem, (2) menjelaskan alur
penanganan isu secara transparan, (3) menampilkan statistik agregat penanganan.
Ini halaman instansi pemerintah, bukan halaman penjualan.

Branding wajib: warna utama #2e7d32, dark #1b5e20, accent #4caf50, surface
#e8f5e9, latar #f7faf7, teks #1c2b21. Logo di public/images/logo.png. Simpan
warna sebagai CSS variable yang sama dengan dashboard. Warna merah/oranye/kuning
hanya untuk badge prioritas (Kritis/Tinggi/Sedang).

Tipografi: Bricolage Grotesque untuk judul, Public Sans untuk isi. Skala
14/16/18/24/32/48/64, panjang baris maksimal 70 karakter, tabular-nums untuk
angka. Semua teks bahasa Indonesia, kalimat biasa (bukan huruf kapital semua),
kata kerja aktif, istilah konsisten (isu, verifikasi, penugasan, intervensi,
penutupan).

Proses: sebelum menulis kode, tulis rencana desain singkat (warna, tipografi,
tata letak dengan wireframe ASCII, prinsip). Periksa apakah ada bagian yang
terasa seperti template generik; jika ada, revisi dan jelaskan perubahannya.
Baru setelah itu bangun halamannya.

Struktur section (rata kiri, lebar maks 1200px):
1. Header: logo + "Sigap Pasar" + "Diskoperindag Prov. Sulawesi Barat", tautan
   Alur / Peran / Wilayah, tombol "Masuk" ke halaman login yang ada.
2. Hero dua kolom. Kiri: judul "Isu pasar yang viral, ditangani sampai tuntas.",
   satu kalimat penjelas, tombol "Masuk ke sistem" dan tautan "Lihat alur
   penanganan". Kanan: kartu "Jalur Penanganan" berlabel "Contoh ilustrasi"
   berisi satu kasus (harga cabai rawit naik di Pasar Mamuju, 2.400 interaksi)
   dengan lima titik status dari Dilaporkan sampai Selesai dan hasil harga
   Rp85.000 -> Rp62.000 per kg. Ini satu-satunya elemen yang boleh dianimasikan
   otomatis: garis progres terisi berurutan saat halaman dimuat (sekitar 2,5
   detik, sekali). Hormati prefers-reduced-motion dengan menampilkan kondisi akhir.
3. Ringkasan penanganan: satu baris tabel sederhana (isu masuk bulan ini, sudah
   selesai, rata-rata waktu penanganan, isu aktif). Ambil dari endpoint agregat;
   jika kosong tampilkan "Belum ada data bulan ini".
4. Alur penanganan: timeline vertikal 5 tahap sesuai bagian 3 dokumen ini. Tiap
   tahap memuat pelaku, pekerjaan, dan hasil. Penomoran boleh dipakai di sini
   karena memang urutan.
5. Peran dan akses: tabel dua kolom untuk 5 peran sesuai bagian 2 dokumen ini.
6. Wilayah: daftar enam kabupaten dengan jumlah isu aktif per wilayah; peta
   sederhana opsional.
7. Jejak audit: satu panel surface #e8f5e9 tentang log aktivitas dan email
   penugasan.
8. Footer #1b5e20: logo, nama lengkap instansi, kontak, tautan Masuk.

Hindari: latar krem + aksen terakota; latar hitam + hijau neon; satu kata di
judul diberi warna sebagai aksen; label huruf kapital berjarak di atas setiap
judul; panah di semua tombol; deretan kartu identik dengan bayangan sama;
gradien dekoratif; animasi muncul-geser-naik di tiap section; hover pada semua
kartu; foto stok; angka atau testimoni karangan.

Kualitas minimum: responsif dari 360px, kontras WCAG AA, fokus keyboard terlihat,
tautan "Lewati ke konten utama", HTML semantik (header/main/section/footer),
alt teks logo "Logo Diskoperindag Provinsi Sulawesi Barat", performa baik di
jaringan seluler. Pakai satu radius dasar 8px (panel besar 12px), sama dengan
dashboard.

Keluaran: kode landing page sesuai stack proyek, daftar file yang dibuat atau
diubah, dan catatan singkat bagian mana yang memakai data contoh dan perlu
dihubungkan ke data nyata.
--- SELESAI PROMPT ---

================================================================================
DOKUMEN SELESAI
================================================================================