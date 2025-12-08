# SIM SOP RSUP Prof. Ngoerah

Sistem Informasi Manajemen Standar Operasional Prosedur (SIM SOP) untuk RSUP Prof. Ngoerah. Aplikasi ini dibangun menggunakan Laravel 12 dan Filament 3, dirancang untuk mengelola dokumen SOP dengan alur verifikasi multi-level dan kontrol akses berbasis role.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [Requirement](#-requirement)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Database Structure](#-database-structure)
- [Role & Panel](#-role--panel)
- [Alur Kerja Sistem](#-alur-kerja-sistem)
- [Troubleshooting](#-troubleshooting)
- [License](#-license)

## ✨ Fitur Utama

### 1. **Multi-Panel Architecture**
- **Panel Admin**: Manajemen master data (User, Role, Direktorat, Unit Kerja)
- **Panel Pengusul**: Pengajuan dan revisi SOP oleh unit kerja
- **Panel Verifikator**: Verifikasi dan approval SOP
- **Panel Viewer**: Melihat SOP yang sudah aktif (read-only)

### 2. **Manajemen SOP**
- Upload dokumen SOP (PDF)
- Dual mode: Draft & Submit
- Kategori SOP: Internal & Antar Profesi (AP)
- Auto-calculate tanggal review dan kadaluwarsa
- Soft delete untuk data history

### 3. **Workflow Verifikasi**
- Status SOP: Draft → Belum Diverifikasi → Aktif/Revisi
- Notifikasi real-time ke verifikator
- History perubahan SOP
- Catatan revisi dari verifikator

### 4. **Notifikasi System**
- Database notifications (bell icon)
- Custom notification table (tb_notifikasi)
- Real-time polling untuk update

### 5. **Access Control**
- Policy-based authorization
- Role-based panel access
- Unit-specific data filtering

## 🛠 Teknologi

- **Framework**: Laravel 12.x
- **PHP**: ^8.2
- **Admin Panel**: Filament 3.3
- **Database**: MySQL/MariaDB
- **PDF Viewer**: joaopaulolndev/filament-pdf-viewer

## 📦 Requirement

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk asset compilation)
- Web Server (Apache/Nginx)

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/idabgsmas/sim-sop-ngoerah.git
cd sim-sop-ngoerah
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sim_sop_ngoerah
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migration
```bash
php artisan migrate
```

### 6. Create Storage Link
```bash
php artisan storage:link
```

### 7. Compile Assets
```bash
npm run build
# atau untuk development
npm run dev
```

### 8. Run Application
```bash
php artisan serve
```

Aplikasi dapat diakses di: `http://localhost:8000`

## ⚙ Konfigurasi

### Panel URLs
- **Admin**: `/admin`
- **Pengusul**: `/pengusul`
- **Verifikator**: `/verifikator`
- **Viewer**: `/viewer`

### Custom Login
Semua panel menggunakan custom login page yang terdapat di:
```
app/Filament/Auth/CustomLogin.php
```

### File Upload
Dokumen SOP disimpan di:
```
storage/app/public/sop-documents/
```

## 🗄 Database Structure

### Tabel Utama

#### Master Data
- `tb_direktorat` - Data direktorat
- `tb_unit_kerja` - Data unit kerja
- `tb_role` - Role pengguna
- `tb_status` - Status SOP

#### User Management
- `tb_user` - Data pengguna
- `tb_unit_user` - Pivot table user-unit (many-to-many)

#### SOP Management
- `tb_sop` - Data SOP utama
- `tb_history_sop` - Riwayat perubahan SOP
- `tb_sop_unit_terkait` - Unit terkait SOP AP

#### Notifikasi
- `tb_notifikasi` - Notifikasi custom
- `notifications` - Laravel database notifications

## 👥 Role & Panel

### 1. Administrator
- **Panel**: Admin
- **Akses**: 
  - Manajemen User
  - Manajemen Role
  - Manajemen Direktorat
  - Manajemen Unit Kerja
  - Master data lainnya

### 2. Pengusul
- **Panel**: Pengusul
- **Akses**:
  - Create SOP (Draft/Submit)
  - Edit SOP (jika status: Draft atau Revisi)
  - Delete SOP (jika status: Draft)
  - View SOP milik unit sendiri

### 3. Verifikator
- **Panel**: Verifikator
- **Akses**:
  - View SOP yang belum diverifikasi
  - Approve SOP → Status Aktif
  - Reject SOP → Status Revisi (dengan catatan)
  - View SOP Aktif & Kadaluwarsa

### 4. Viewer
- **Panel**: Viewer
- **Akses**:
  - View SOP Aktif (read-only)
  - Download dokumen SOP

## 🔄 Alur Kerja Sistem

```
┌──────────────┐
│  PENGUSUL    │
│  Create SOP  │
└──────┬───────┘
       │
       ├─→ Draft (dapat diedit/dihapus kapan saja)
       │
       ├─→ Submit (Status: Belum Diverifikasi)
       │
       v
┌──────────────┐
│ VERIFIKATOR  │
│  Review SOP  │
└──────┬───────┘
       │
       ├─→ Approve → Status: AKTIF ✓
       │
       └─→ Reject → Status: REVISI (kembali ke Pengusul)
                    │
                    └─→ Pengusul perbaiki → Submit lagi
```

## 🔐 Seeding Data

Untuk testing, Anda dapat membuat seeder untuk data awal:

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder StatusSeeder
php artisan make:seeder UserSeeder
```

### Contoh Status SOP
```php
// StatusSeeder.php
DB::table('tb_status')->insert([
    ['id_status' => 1, 'nama_status' => 'Draft'],
    ['id_status' => 2, 'nama_status' => 'Belum Diverifikasi'],
    ['id_status' => 3, 'nama_status' => 'Dalam Revisi'],
    ['id_status' => 4, 'nama_status' => 'Aktif'],
    ['id_status' => 5, 'nama_status' => 'Kadaluwarsa'],
]);
```

### Contoh Role
```php
// RoleSeeder.php
DB::table('tb_role')->insert([
    ['id_role' => 1, 'nama_role' => 'Administrator'],
    ['id_role' => 2, 'nama_role' => 'Pengusul'],
    ['id_role' => 3, 'nama_role' => 'Verifikator'],
    ['id_role' => 4, 'nama_role' => 'Viewer'],
]);
```

Jalankan seeder:
```bash
php artisan db:seed
```

## 🐛 Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
php artisan optimize:clear
```

### Error: Storage link tidak berfungsi
```bash
php artisan storage:link
```

### Error: Permission denied (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Error: Permission denied (Windows)
Pastikan folder `storage` dan `bootstrap/cache` memiliki permission write.

### Notifikasi tidak muncul
Pastikan:
1. Trait `HasDatabaseNotifications` ada di model `TbUser`
2. Migration `notifications` sudah dijalankan
3. Panel sudah enable `->databaseNotifications()`

## 📝 Development Notes

### Validasi SOP
- Form menggunakan validasi nullable untuk support Draft mode
- Validasi ketat diterapkan di `mutateFormDataBeforeCreate()` dan `mutateFormDataBeforeSave()`
- Dual button: "Simpan Draft" (validasi longgar) vs "Kirim" (validasi ketat)

### File Upload
- FileUpload component Filament otomatis handle array
- Perlu unwrap array di action Draft untuk menghindari error insert

### Policy
- SOP Policy mengatur akses berdasarkan role dan status
- Pengusul hanya bisa edit SOP dengan status: Draft (1) atau Revisi (3)
- Verifikator dapat approve/reject SOP dengan status: Belum Diverifikasi (2)

## 🤝 Contributing

Untuk kontribusi pada project ini:

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'feat: add amazing feature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is proprietary software for RSUP Prof. Ngoerah.

---

**Developed by**: Tim IT RSUP Prof. Ngoerah  
**Last Updated**: December 2025  
**Version**: 1.0.0
