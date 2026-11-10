# Introduction

REST API Sistem Pengelolaan Kendaraan Dinas Operasional (KDO) Perangkat Daerah — JP-1 (20270311).

<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>

    API ini digunakan untuk mengelola data kendaraan dinas operasional (KDO) milik perangkat daerah,
    mencakup pendataan kendaraan beserta kondisinya, data pegawai negeri sipil (PNS) selaku
    penanggung jawab penggunaan, serta data perangkat daerah (instansi).

    Seluruh endpoint (kecuali login) membutuhkan bearer token Sanctum. Role **admin** dapat
    melakukan seluruh aksi, sedangkan **viewer** hanya dapat membaca data.

