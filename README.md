# Sistem Bayaran Yuran Pelajar

Projek kajian kes untuk **Kursus Laravel Advanced**. Satu aplikasi Laravel 13
yang sama digunakan dari Modul 1 hingga Modul 6: pelajar membayar invois yuran
(FPX, kad, e-wallet, kaunter), dan Pejabat Bendahari memantau kutipan.

## Keperluan

| Alat | Versi |
|---|---|
| PHP | 8.4 atau lebih baharu |
| Composer | 2.x |
| MySQL / MariaDB | MySQL 8.x atau MariaDB 11.x |
| Redis | 7.x (mulai Modul 2) |
| Git | Git for Windows, guna **Git Bash** sebagai terminal |

## Persediaan (sekali sahaja)

```bash
git clone https://github.com/hymns/sistem-bayaran-yuran.git
cd sistem-bayaran-yuran
git switch modul-1-start

composer install
cp .env.example .env
php artisan key:generate

# cipta pangkalan data kosong bernama sistem_yuran, kemudian:
php artisan migrate --seed
php artisan test
```

Semua ujian mesti hijau sebelum anda mula.

### Akaun ujian (kata laluan: `rahsia123`)

| Emel | Peranan |
|---|---|
| `aisyah@graduate.utm.my` | Pelajar, matrik A23CS0012 |
| `badrul@graduate.utm.my` | Pelajar, matrik A23CS0057 |
| `siti@utm.my` | Puan Siti, Pejabat Bendahari |

Invois 7 milik Badrul (asrama, RM480, lewat). Invois 6 milik Aisyah.

## Branch setiap modul

Setiap modul bermula dari branch `modul-N-start`. Branch itu ialah kod akhir
modul sebelumnya, jadi anda boleh mula dengan bersih walaupun lab sebelum ini
belum siap.

| Branch | Untuk |
|---|---|
| `modul-1-start` | Modul 1: `PaymentController` asal yang "buat semua benda", dengan ujian feature yang lulus |
| `modul-2-start` | Modul 2: kod akhir Modul 1, halaman `/bursary/payments`, `LargeFeeSeeder` |
| `modul-2-lab` | Lab Modul 2: dashboard `/bursary/overdue` sebelum optimasi, `lab:double-click` |
| `modul-3-start` | Modul 3: kod akhir Modul 2, Sanctum dipasang |
| `modul-3-lab` | Lab 3 Modul 3: API yang berfungsi, dengan lima lubang keselamatan |
| `modul-4-start` | Modul 4: kod akhir Modul 3, eksport laporan segerak |
| `modul-5-start` | Modul 5: kod akhir Modul 4, factory dengan state |
| `modul-6-start` | Modul 6: kod akhir Modul 5, `ReceiptController` untuk code review |
| `selesai` | Kod akhir kursus, termasuk `docs/adr/` |

## Cara kerja dalam kursus

Repo ini milik trainer. Anda **tidak** push ke sini. Kerja anda kekal dalam
salinan anda sendiri.

```bash
# simpan kerja anda sebelum bertukar modul
git add -A
git commit -m "Lab Modul 1"

# ambil branch modul seterusnya dari trainer
git fetch origin
git switch modul-2-start

composer install
php artisan migrate:fresh --seed
php artisan test
```

Mahu simpan kerja di GitHub anda sendiri? Klik **Fork** di GitHub, kemudian:

```bash
git remote add saya https://github.com/<username-anda>/sistem-bayaran-yuran.git
git push saya HEAD
```

## Data besar (Modul 2 dan 4)

```bash
php artisan db:seed --class=LargeFeeSeeder
```

Kira-kira 20,000 pelajar dan 200,000 invois. Nombor sebenar dalam slide datang
daripada data ini; nombor anda akan hampir sama, tetapi tidak tepat sama.

## Nota

- `.env` tidak pernah di-commit. Jangan letak kunci sebenar dalam repo ini.
- Gateway bayaran tidak menghubungi bank sebenar. Ujian guna `Http::fake()`.
