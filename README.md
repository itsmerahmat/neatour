# NeaTour - Platform Wisata Alam Nusantara

[![Live Demo](https://img.shields.io/badge/Live%20Demo-neatour.laravel.cloud-brightgreen)](https://neatour.laravel.cloud/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5-green.svg)](https://vuejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.2-blue.svg)](https://www.typescriptlang.org/)

## 📝 Deskripsi

**NeaTour** adalah platform digital yang dirancang khusus untuk membantu wisatawan menemukan dan menjelajahi destinasi wisata alam terbaik di Indonesia. Aplikasi ini menggabungkan teknologi modern dengan antarmuka yang intuitif untuk memberikan pengalaman wisata yang tak terlupakan.

### ✨ Fitur Utama

- 🗺️ **Peta Interaktif** - Temukan destinasi wisata terdekat dengan lokasi real-time
- 📍 **Pencarian Berbasis Lokasi** - Sortir destinasi berdasarkan jarak dari lokasi Anda
- ⭐ **Sistem Rating & Review** - Baca dan tulis ulasan dari sesama traveler
- 🔍 **Filter Pencarian Canggih** - Filter berdasarkan kategori, rating, dan lokasi
- 📱 **Responsive Design** - Optimized untuk desktop, tablet, dan mobile
- 🖼️ **Galeri Foto Berkualitas Tinggi** - Powered by ImageKit untuk performa optimal
- 👥 **Multi-User Management** - Sistem role untuk admin dan superadmin
- 📊 **Dashboard Admin** - Kelola destinasi, kategori, dan ulasan dengan mudah

## 🛠️ Teknologi

### Backend
- **Laravel 12.x** - Framework PHP modern dengan fitur terbaru
- **Inertia.js** - SPA experience tanpa kompleksitas API
- **ImageKit** - Image processing dan CDN untuk performa optimal
- **MySQL** - Database relasional yang andal

### Frontend
- **Vue.js 3.5** - Progressive JavaScript framework
- **TypeScript** - Type-safe JavaScript development
- **Tailwind CSS 4.x** - Utility-first CSS framework
- **Reka UI** - Component library yang modern dan accessible
- **Leaflet** - Interactive maps
- **Vue Quill** - Rich text editor untuk konten

### Tools & DevOps
- **Vite** - Fast build tool dan dev server
- **ESLint & Prettier** - Code linting dan formatting
- **Pest** - Testing framework untuk PHP
- **Laravel Pint** - Code style fixer

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js 18+ & npm/yarn
- MySQL/PostgreSQL
- ImageKit account (untuk image processing)

### Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/username/neatour.git
   cd neatour
   ```

2. **Install dependencies**
   ```bash
   # Install PHP dependencies
   composer install
   
   # Install JavaScript dependencies
   npm install
   ```

3. **Setup environment**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

4. **Konfigurasi database**
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=neatour
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Konfigurasi ImageKit**
   Daftar di [ImageKit.io](https://imagekit.io) dan tambahkan kredensial ke `.env`:
   ```env
   IMAGEKIT_PUBLIC_KEY=your_public_key
   IMAGEKIT_PRIVATE_KEY=your_private_key
   IMAGEKIT_URL_ENDPOINT=https://ik.imagekit.io/your_imagekit_id/
   ```

6. **Migrate database**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start development server**
   ```bash
   # Terminal 1: Laravel server
   php artisan serve
   
   # Terminal 2: Vite dev server
   npm run dev
   ```

## 🏗️ Struktur Aplikasi

### Model & Database
- **User** - Manajemen pengguna dengan role-based access
- **Destination** - Destinasi wisata dengan lokasi GPS dan media
- **Category** - Kategorisasi destinasi (Pantai, Gunung, Air Terjun, dll)
- **Testimonial** - Ulasan dan rating dari pengunjung

### Role & Permissions
- **Superadmin** - Akses penuh ke semua fitur dan data
- **Admin** - Kelola destinasi dan testimonial yang mereka tangani
- **User** - Akses publik untuk melihat dan memberikan ulasan

### API Integration
- **ImageKit** - Upload, resize, dan optimasi gambar otomatis
- **Geolocation** - Deteksi lokasi pengguna untuk pencarian terdekat
- **Google Maps** - Embed peta untuk detail lokasi

## 📱 Fitur Lengkap

### Untuk Wisatawan
- ✅ Jelajahi destinasi wisata alam terpopuler
- ✅ Temukan wisata terdekat berdasarkan lokasi GPS
- ✅ Filter berdasarkan kategori dan rating
- ✅ Baca review dan rating dari pengunjung lain
- ✅ Tulis ulasan dan berikan rating
- ✅ Lihat peta lokasi dan petunjuk arah
- ✅ Akses informasi lengkap (jam operasional, fasilitas, alamat)

### Untuk Admin
- ✅ Dashboard manajemen destinasi
- ✅ Upload dan kelola foto destinasi
- ✅ Moderasi ulasan dan testimonial
- ✅ Analytics dan laporan kunjungan
- ✅ Manajemen kategori wisata

### Untuk Superadmin
- ✅ Semua fitur admin
- ✅ Manajemen pengguna dan role
- ✅ Kontrol penuh atas semua destinasi
- ✅ Konfigurasi sistem dan settings

## 🔧 Development

### Available Scripts
```bash
# Development
npm run dev          # Start Vite dev server
php artisan serve    # Start Laravel development server

# Build
npm run build        # Build for production
npm run build:ssr    # Build with SSR support

# Code Quality
npm run lint         # Run ESLint
npm run format       # Format code with Prettier
composer pint        # Fix PHP code style

# Testing
php artisan test     # Run PHP tests
./vendor/bin/pest    # Run Pest tests
```

### Project Structure
```
├── app/
│   ├── Http/Controllers/     # API & Web controllers
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Traits/              # Reusable traits
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── js/
│   │   ├── components/      # Vue components
│   │   ├── pages/           # Inertia pages
│   │   └── types/           # TypeScript types
│   └── views/               # Blade templates
├── routes/
│   ├── web.php              # Web routes
│   └── auth.php             # Authentication routes
└── public/                  # Static assets
```

## 🌟 Contributing

Kami menyambut kontribusi dari developer untuk meningkatkan platform NeaTour!

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit perubahan (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

### Development Guidelines
- Ikuti PSR-12 coding standard untuk PHP
- Gunakan ESLint dan Prettier untuk JavaScript/TypeScript
- Tulis tests untuk fitur baru
- Update dokumentasi jika diperlukan

## 📄 License

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## 🤝 Support

- 📧 Email: support@neatour.com
- 🐛 Issues: [GitHub Issues](https://github.com/username/neatour/issues)
- 📖 Documentation: [Wiki](https://github.com/username/neatour/wiki)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/) - Framework PHP yang luar biasa
- [Vue.js](https://vuejs.org/) - Progressive JavaScript framework
- [ImageKit](https://imagekit.io/) - Image processing dan CDN
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework
- Komunitas open source Indonesia

---

**NeaTour** - *Temukan Keindahan Alam Nusantara* 🌿🏔️🏖️