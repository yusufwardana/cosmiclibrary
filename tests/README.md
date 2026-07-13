# 🧪 CosmicLib Tests

Direktori ini dirancang untuk menampung kerangka pengujian otomatis (automated testing) guna menjamin fungsionalitas core system dan modul sirkulasi tetap berjalan dengan sempurna.

## Pembagian Uji (Testing Strategy)
1. **Unit Tests**: Menguji logika bisnis murni pada kelas Service terisolasi (misal kalkulasi denda, masa pinjam).
2. **Feature Tests**: Menguji fungsionalitas HTTP endpoint secara end-to-end (misal simulasi pendaftaran anggota baru, transaksi sirkulasi via endpoint API).
3. **Integration Tests**: Menguji keselarasan antarmodul dinamis dan kestabilan pemuatan service provider.

Pengujian akan dilakukan menggunakan kerangka **Pest PHP** atau **PHPUnit** bawaan Laravel 12.
