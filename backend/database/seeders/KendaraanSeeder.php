<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    /**
     * Data kendaraan dinas operasional — beberapa di antaranya
     * belum memiliki penanggung jawab (pegawai_id = null).
     */
    public function run(): void
    {
        // nip => penanggung jawab (null artinya belum ditunjuk)
        $data = [
            ['nomor_polisi' => 'DK 1234 AB', 'merek' => 'Toyota', 'tipe' => 'Avanza 1.5 G', 'tahun_produksi' => 2021, 'warna' => 'Putih', 'kondisi' => 'baik', 'kode_pd' => 'SETDA', 'nip' => '197504121998031001'],
            ['nomor_polisi' => 'DK 5678 CD', 'merek' => 'Toyota', 'tipe' => 'Hiace Commuter', 'tahun_produksi' => 2019, 'warna' => 'Putih', 'kondisi' => 'rusak_ringan', 'kode_pd' => 'SETDA', 'nip' => null],

            ['nomor_polisi' => 'DK 2345 EF', 'merek' => 'Mitsubishi', 'tipe' => 'Pajero Sport Dakar', 'tahun_produksi' => 2020, 'warna' => 'Hitam', 'kondisi' => 'baik', 'kode_pd' => 'DISDIK', 'nip' => '196811051994031004'],
            ['nomor_polisi' => 'DK 3456 GH', 'merek' => 'Toyota', 'tipe' => 'Kijang Innova Reborn', 'tahun_produksi' => 2018, 'warna' => 'Silver', 'kondisi' => 'baik', 'kode_pd' => 'DISDIK', 'nip' => '199001172015032007'],
            ['nomor_polisi' => 'DK 7890 IJ', 'merek' => 'Suzuki', 'tipe' => 'Ertiga', 'tahun_produksi' => 2017, 'warna' => 'Abu-abu', 'kondisi' => 'rusak_ringan', 'kode_pd' => 'DISDIK', 'nip' => '198506302010121003'],

            ['nomor_polisi' => 'DK 4567 KL', 'merek' => 'Toyota', 'tipe' => 'Hiace Ambulance', 'tahun_produksi' => 2020, 'warna' => 'Putih', 'kondisi' => 'baik', 'kode_pd' => 'DINKES', 'nip' => '197208151999022001'],
            ['nomor_polisi' => 'DK 8901 MN', 'merek' => 'Nissan', 'tipe' => 'Urvan', 'tahun_produksi' => 2016, 'warna' => 'Putih', 'kondisi' => 'rusak_berat', 'kode_pd' => 'DINKES', 'nip' => '199303282018101002'],
            ['nomor_polisi' => 'DK 1122 OP', 'merek' => 'Honda', 'tipe' => 'BR-V', 'tahun_produksi' => 2022, 'warna' => 'Merah', 'kondisi' => 'baik', 'kode_pd' => 'DINKES', 'nip' => null],

            ['nomor_polisi' => 'DK 2233 QR', 'merek' => 'Toyota', 'tipe' => 'Fortuner VRZ', 'tahun_produksi' => 2021, 'warna' => 'Hitam', 'kondisi' => 'baik', 'kode_pd' => 'DISHUB', 'nip' => '197907092006041005'],
            ['nomor_polisi' => 'DK 3344 ST', 'merek' => 'Daihatsu', 'tipe' => 'Gran Max Minibus', 'tahun_produksi' => 2018, 'warna' => 'Putih', 'kondisi' => 'rusak_ringan', 'kode_pd' => 'DISHUB', 'nip' => '198812012013062001'],
            ['nomor_polisi' => 'DK 5566 UV', 'merek' => 'Isuzu', 'tipe' => 'Elf Long Chasis', 'tahun_produksi' => 2015, 'warna' => 'Putih', 'kondisi' => 'rusak_berat', 'kode_pd' => 'DISHUB', 'nip' => null],

            ['nomor_polisi' => 'DK 6677 WX', 'merek' => 'Toyota', 'tipe' => 'Hilux Double Cabin', 'tahun_produksi' => 2020, 'warna' => 'Putih', 'kondisi' => 'baik', 'kode_pd' => 'DPUPR', 'nip' => '197402202000011006'],
            ['nomor_polisi' => 'DK 8899 YZ', 'merek' => 'Mitsubishi', 'tipe' => 'Triton HDX', 'tahun_produksi' => 2019, 'warna' => 'Putih', 'kondisi' => 'rusak_ringan', 'kode_pd' => 'DPUPR', 'nip' => '199205162016112003'],

            ['nomor_polisi' => 'DK 9900 AA', 'merek' => 'Toyota', 'tipe' => 'Vios', 'tahun_produksi' => 2018, 'warna' => 'Silver', 'kondisi' => 'baik', 'kode_pd' => 'BKD', 'nip' => '198103112008091004'],
            ['nomor_polisi' => 'DK 1010 BC', 'merek' => 'Daihatsu', 'tipe' => 'Luxio', 'tahun_produksi' => 2016, 'warna' => 'Hitam', 'kondisi' => 'baik', 'kode_pd' => 'BKD', 'nip' => null],
        ];

        foreach ($data as $item) {
            $pd = PerangkatDaerah::where('kode', $item['kode_pd'])->first();
            $pegawaiId = null;

            if ($item['nip'] !== null) {
                $pegawaiId = Pegawai::where('nip', $item['nip'])->value('id');
            }

            Kendaraan::create([
                'nomor_polisi' => $item['nomor_polisi'],
                'merek' => $item['merek'],
                'tipe' => $item['tipe'],
                'tahun_produksi' => $item['tahun_produksi'],
                'warna' => $item['warna'],
                'kondisi' => $item['kondisi'],
                'perangkat_daerah_id' => $pd->id,
                'pegawai_id' => $pegawaiId,
            ]);
        }
    }
}
