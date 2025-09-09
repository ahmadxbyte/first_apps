# 🔹 First Apps – CodeIgniter 3 Project

## 📖 Introduction
First Apps adalah project berbasis CodeIgniter 3, sebuah PHP framework ringan dan powerful untuk membangun aplikasi web dengan cepat. CodeIgniter menyediakan library umum, struktur MVC yang sederhana, serta dokumentasi lengkap untuk memudahkan pengembangan.

## ⚙️ Installation
Pastikan Composer sudah terpasang di komputer. Untuk menginstal library yang dibutuhkan jalankan perintah:

    composer require mpdf/mpdf
    composer require chillerlan/php-qrcode

Untuk pengguna macOS jalankan perintah berikut agar folder `tmp` dapat diakses:

    sudo chmod -R 777 vendor/mpdf/mpdf/tmp

Jika Composer tidak dapat digunakan, vendor package dapat diunduh melalui link berikut:  
[Download Vendor](https://drive.google.com/file/d/1uiw_qQ5H5KZOPORM36l0OjDUIoFZzfuX/view?usp=drive_link)  

Setelah itu ekstrak hasil download ke folder project. Tambahkan juga folder `assets/` dan `file/pdf/` ke dalam project agar sistem berjalan dengan baik.

## 📌 Project Information
Bahasa pemrograman yang digunakan adalah PHP dan JavaScript dengan database MySQL. Beberapa library yang digunakan antara lain mPDF sebagai PDF Generator, chillerlan/php-qrcode sebagai QR Code Generator, Select2 untuk input yang lebih interaktif, dan DataTables untuk pengelolaan tabel.

## 🔗 Resources
- [CodeIgniter User Guide](https://codeigniter.com/docs)  
- [CodeIgniter Downloads](https://codeigniter.com/download)  
- [Contributing Guide](https://github.com/bcit-ci/CodeIgniter/blob/develop/contributing.md)  
- [Language File Translations](https://github.com/bcit-ci/codeigniter3-translations)  
- [Community Forum](http://forum.codeigniter.com/)  
- [Community Wiki](https://github.com/bcit-ci/CodeIgniter/wiki)  
- [Slack Channel](https://codeigniterchat.slack.com)  

## 📜 License
Project ini menggunakan lisensi resmi dari CodeIgniter. Detail lisensi dapat dilihat di [CodeIgniter License](https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/license.rst).

## 🙏 Acknowledgement
Terima kasih kepada Tim CodeIgniter, semua kontributor open-source, dan seluruh pengguna yang mendukung pengembangan project ini.
