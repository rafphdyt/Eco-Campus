# Eco-Campus API

Semua endpoint memakai prefix /api dan mengembalikan JSON dengan bentuk dasar:

- Berhasil: {"status":"success","message":"...","data":{...}}
- Gagal: {"status":"error","message":"...","errors":{...}} (errors muncul saat validasi gagal)

Endpoint selain daftar publik memerlukan header Authorization: Bearer <token>.

Token diberikan pada data.token oleh POST /api/register atau POST /api/login. Token disimpan sebagai hash di server dan dicabut oleh POST /api/logout.

## Publik dan autentikasi

| Method | Endpoint | Kegunaan |
| --- | --- | --- |
| GET | /api/institutions | Daftar institusi |
| GET | /api/departments?institution_id=1 | Daftar jurusan, dapat difilter menurut institusi |
| POST | /api/register | Membuat akun mahasiswa |
| POST | /api/login | Login |
| POST | /api/logout | Logout dan mencabut token |
| GET | /api/account | Melihat profil |
| PUT atau POST | /api/account | Memperbarui nama, NPM, jurusan, dan foto |
| DELETE | /api/account | Menghapus akun dengan konfirmasi kata sandi |

Contoh body pendaftaran:

    {
      "name": "Nama Mahasiswa",
      "npm": "140810250001",
      "department_id": 1,
      "email": "mahasiswa@example.ac.id",
      "password": "rahasia123"
    }

Saat memperbarui profil dengan foto, kirim multipart/form-data dengan field photo, name, npm, dan department_id. Foto dibatasi 2 MB.

## Fitur mahasiswa

| Method | Endpoint | Kegunaan |
| --- | --- | --- |
| GET | /api/dashboard | Data profil, tantangan aktif, dan lima artikel terbaru |
| GET | /api/library | Artikel dan kategori; mendukung category_id dan per_page |
| GET | /api/library/articles/{article} | Detail artikel |
| GET | /api/challenges?board=orang | Tantangan aktif serta papan peringkat |
| POST | /api/challenges/{challenge}/submissions | Mengirim foto bukti tantangan |
| GET | /api/challenges/history | Riwayat bukti; mendukung filter status dan per_page |
| GET | /api/leaderboard?board=jurusan | Papan peringkat |

Nilai board: orang, jurusan, atau fakultas. Nilai status riwayat: pending, approved, atau rejected.

Untuk mengirim bukti, gunakan multipart/form-data dengan field proof berisi gambar maksimal 2 MB. Setiap mahasiswa dapat mengirim satu bukti per tantangan per hari selama bukti yang ada belum ditolak. Poin baru bertambah setelah admin menyetujuinya.

## Fitur admin

Semua endpoint admin memerlukan token milik akun dengan role admin.

| Method | Endpoint | Kegunaan |
| --- | --- | --- |
| GET | /api/admin/dashboard?board=orang | Ringkasan statistik dan papan peringkat |
| GET | /api/admin/challenges | Daftar tantangan |
| POST | /api/admin/challenges | Membuat tantangan |
| PUT atau PATCH | /api/admin/challenges/{challenge} | Memperbarui tantangan |
| PATCH | /api/admin/challenges/{challenge}/toggle | Mengaktifkan atau menonaktifkan tantangan |
| DELETE | /api/admin/challenges/{challenge} | Menghapus tantangan |
| GET | /api/admin/categories | Daftar kategori artikel |
| GET | /api/admin/articles | Daftar artikel |
| POST | /api/admin/articles | Membuat artikel |
| PUT atau PATCH | /api/admin/articles/{article} | Memperbarui artikel |
| DELETE | /api/admin/articles/{article} | Menghapus artikel |
| GET | /api/admin/proofs?status=pending | Daftar bukti masuk |
| PATCH | /api/admin/proofs/{proof}/approve | Menyetujui bukti dan menambahkan poin |
| PATCH | /api/admin/proofs/{proof}/reject | Menolak bukti; poin yang pernah diberikan akan dikurangi |

Daftar admin mendukung parameter per_page (1 sampai 50). Status bukti dapat berupa all, pending, approved, atau rejected.

## Catatan respons

Validasi gagal memakai HTTP 422, token tidak sah memakai HTTP 401, akses admin tanpa peran admin memakai HTTP 403, dan pengiriman bukti duplikat memakai HTTP 409. Respons daftar menyertakan objek pagination berisi current_page, last_page, per_page, dan total.