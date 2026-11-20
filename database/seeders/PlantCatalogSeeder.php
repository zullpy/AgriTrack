<?php

namespace Database\Seeders;

use App\Models\PlantCatalog;
use App\Models\PlantCatalogGuide;
use App\Models\PlantCatalogHama;
use Illuminate\Database\Seeder;

class PlantCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $plants = [
            [
                'catalog' => [
                    'key' => 'timun',
                    'name' => 'Timun',
                    'varietas' => 'Hibrida F1 (Hercules, Metavy, Panda)',
                    'emoji' => '🥒',
                    'cycle' => '35 – 45 Hari',
                    'deskripsi' => 'Tanaman merambat semusim yang sangat responsif terhadap kelembaban air dan nutrisi seimbang. Membutuhkan ajir/lanjaran untuk hasil buah lurus dan mulus.',
                    'theme' => 'emerald',
                    'keywords' => 'timun, mentimun, cucumber',
                    'urutan' => 1,
                    'aktif' => true,
                    'tahap_semai' => "1. Seleksi & Perendaman Benih:\n• Rendam benih dalam air hangat kuku (40–45°C) selama 2–4 jam.\n• Campurkan sedikit ekstrak bawang merah (ZPT alami) untuk memecah masa dormansi dan merangsang bakal akar.\n• Tiriskan benih di atas tisu atau kain basah hingga calon akar (radikula) putih mulai tampak (12–24 jam).\n\n2. Media Semai:\n• Gunakan campuran tanah subur remah, arang sekam, dan kompos halus terfermentasi (perbandingan 2:1:1).\n• Masukkan ke dalam tray semai (lubang 98/128) atau polybag kecil ukuran 6x8 cm.\n\n3. Pemeliharaan Semai:\n• Masukkan 1 benih per lubang sedalam 1 cm dengan posisi calon akar menghadap ke bawah, lalu tutup tipis.\n• Siram dengan semprotan kabut halus di pagi hari agar media lembap tapi tidak becek.\n• Bibit timun tumbuh sangat cepat dan siap pindah tanam pada umur 7–10 hari (setelah muncul 2 daun sejati).",
                    'tahap_penanaman' => "1. Persiapan Bedengan:\n• Buat bedengan lebar 100 cm, tinggi 30–40 cm dengan saluran parit drainase yang lancar.\n• Pasang mulsa plastik perak-hitam (MPHP) dan buat lubang tanam dengan pelubang arang.\n\n2. Jarak Tanam Ideal:\n• Jarak tanam timun: 40–50 cm antar tanaman dalam barisan, dan 60–70 cm antar baris (pola baris ganda/segitiga).\n\n3. Waktu & Cara Transplanting:\n• Lakukan pindah tanam pada sore hari (pukul 15.30 ke atas) untuk mencegah stres layu akibat terik siang.\n• Siram tray semai 1 jam sebelum pindah tanam agar bola tanah tidak pecah saat dicabut.\n• Masukkan bibit tegak lurus ke lubang tanam, tutup tanah setinggi leher akar, dan kocor air bersih secukupnya.",
                    'tahap_pemeliharaan' => "1. Pemasangan Ajir/Lanjaran:\n• Pasang ajir bambu setinggi 1.8–2 meter pada umur 5–7 HST sebelum akar menyebar luas.\n• Pasang tali tambang/jaring lanjaran dan bantu rambatkan sulur timun pertama ke atas.\n\n2. Pemangkasan (Pruning) Tunas Air:\n• Rempel seluruh tunas samping (tunas air) dari ketiak daun ke-1 sampai ke-5 dari bawah.\n• Hal ini bertujuan agar sirkulasi udara di bawah lancar, mencegah jamur daun, dan memicu batang utama bertumbuh kokoh.\n\n3. Penyiraman & Pemupukan:\n• Timun rakus air namun sangat rentan akar busuk jika tergenang. Siram rutin setiap 1–2 hari sekali.\n• Fase vegetatif (7–20 HST): Kocor NPK 16-16-16 dosis 5 gram/liter seminggu sekali.\n• Fase generatif (25 HST ke atas): Berikan pupuk tinggi Kalium & Kalsium (MKP, KNO3 putih, Kalsium Boron) untuk mempercepat pengisian buah dan mencegah kerontokan.",
                    'tips_penting' => "• Petik buah timun secara rutin setiap 2 hari sekali di pagi hari. Membiarkan buah tua tertinggal di pohon akan menghentikan pembentukan bunga betina baru.\n• Defisiensi Kalsium & Boron menyebabkan ujung buah timun mengecil (bengkok lancip) atau busuk pantat buah.\n• Pasang perangkap kuning (yellow sticky trap) dan lalat buah sejak awal untuk mencegah buah berlubang atau busuk bintik.",
                    'hama_penyakit' => "1. Hama Utama & Pengendalian:\n• Kutu Daun (Aphis) & Thrips:\n  - Gejala: Daun melengkung ke bawah, keriting, menguning kusam, dan tanaman kerdil.\n  - Solusi: Semprot insektisida bahan aktif Abamektin (0.5–1 ml/L) atau Imidakloprid bergantian, utamakan bagian bawah daun sore hari.\n• Lalat Buah (Bactrocera cucurbitae):\n  - Gejala: Titik hitam pada buah muda, buah bengkok, busuk dan berbelatung di dalam.\n  - Solusi: Pasang perangkap atraktan metil eugenol (Petrogenol) di luar bedengan, semprot insektisida berbahan aktif Dimetoat atau Sipermetrin.\n• Kumbang Daun (Oteng-oteng / Aulacophora):\n  - Gejala: Daun berlubang bulat membentuk pola cincin.\n  - Solusi: Semprot insektisida kontak Karbaril atau Deltametrin saat pagi hari.\n\n2. Penyakit Utama & Solusi:\n• Embun Bulu / Downy Mildew (Pseudoperonospora cubensis):\n  - Gejala: Bercak kuning bersegi pada permukaan daun, bagian bawah daun berbulu abu-abu keunguan, daun cepat kering terbakar.\n  - Solusi: Fungisida sistemik Dimetomorf atau Simoksanil dikombinasikan fungisida kontak Mankozeb / Klorotalonil.\n• Layu Bakteri (Ralstonia) & Layu Fusarium:\n  - Gejala: Layu mendadak di siang hari dan segar kembali di pagi hari (fase awal), batang bawah kecoklatan.\n  - Solusi: Kocor Trichoderma sp. sejak olah tanah, gunakan bakterisida Tembaga Oksiklorida atau Streptomisin sulfat bila terinfeksi.",
                ],
                'guides' => [
                    [
                        'phase' => 'Pemupukan Dasar',
                        'hst' => '0 HST (Sebelum Tanam)',
                        'focus' => 'Membangun struktur tanah & stok hara awal',
                        'nutrients' => ['Pupuk Kandang / Kompos', 'NPK 16-16-16', 'SP-36', 'Dolomit'],
                        'dosis' => 'Pupuk kandang 1 kg/lubang + NPK 10 gr/lubang + SP-36 5 gr/lubang.',
                        'metode' => 'Campur rata dalam lubang tanam, tutup tipis dengan tanah sebelum benih/bibit dimasukkan.',
                        'tips' => 'Pastikan pH tanah 6.0–6.8. Tanah masam hambat penyerapan P dan Ca secara signifikan.',
                        'urutan' => 1,
                    ],
                    [
                        'phase' => 'Vegetatif Awal',
                        'hst' => '7 – 14 HST',
                        'focus' => 'Memacu pertumbuhan akar & sulur merambat',
                        'nutrients' => ['NPK 16-16-16', 'Asam Humat', 'ZA (Nitrogen)'],
                        'dosis' => '5 gram NPK per liter air, kocor 200 ml per tanaman.',
                        'metode' => 'Kocor di sekitar perakaran, hindari terkena batang langsung.',
                        'tips' => 'Pasang ajir/tali rambatan lebih awal agar sulur tidak tumbuh ke tanah.',
                        'urutan' => 2,
                    ],
                ],
            ],

            [
                'catalog' => [
                    'key' => 'cabe',
                    'name' => 'Cabe',
                    'varietas' => 'Cabai Rawit / Keriting (Ori 212, Imola F1, Kastilo)',
                    'emoji' => '🌶️',
                    'cycle' => '75 – 120 Hari',
                    'deskripsi' => 'Komoditas hortikultura bernilai ekonomi tinggi. Memerlukan penanganan hama thrips serta pengaturan kelembapan bedengan yang presisi untuk panen melimpah.',
                    'theme' => 'rose',
                    'keywords' => 'cabe, cabai, chili, lombok',
                    'urutan' => 2,
                    'aktif' => true,
                    'tahap_semai' => "1. Perendaman & Perlakuan Benih:\n• Rendam benih cabe dalam air hangat bersuhu 45–50°C selama 4–6 jam.\n• Tambahkan fungisida perlakuan benih (misal berbahan aktif mankozeb atau karbendazim) untuk mencegah rebah kecambah (damping-off).\n• Bungkus benih dengan kain/kertas koran basah selama 24–48 jam di tempat hangat gelap hingga titik radikula putih keluar.\n\n2. Media Semai & Tray:\n• Campuran media: tanah remah ayak halus + arang sekam + pupuk organik matang (1:1:1).\n• Masukkan ke dalam tray semai lubang 98 atau polybag semai kecil.\n• Masukkan 1 benih per lubang sedalam 0.5 cm, tutup dengan media ayakan halus.\n\n3. Perawatan Semai Cabe:\n• Sungkup tray dengan plastik gelap atau karung basah selama 3 hari pertama sampai kecambah muncul.\n• Setelah berkecambah, buka sungkup dan berikan naungan paranet transparan.\n• Aklimatisasi (Hardening-off): Pada umur 18–25 hari, kenalkan bibit ke sinar matahari penuh dan kurangi penyiraman agar batang cabe mengeras dan berkayu.",
                    'tahap_penanaman' => "1. Standar Bibit Siap Tanam:\n• Bibit cabe berumur 21–28 hari setelah semai, memiliki 4–6 helai daun sejati, batang tegak kekar (tidak jangkung kurus).\n\n2. Jarak Tanam Bedengan:\n• Jarak tanam ideal: 50 cm x 60 cm zig-zag (segitiga) pada bedengan bermulsa hitam-perak.\n• Pola zig-zag memberi ruang penerimaan sinar matahari maksimal dan memperlancar sirkulasi udara antar pokok.\n\n3. Pelaksanaan Tanam:\n• Tanam bibit di lubang mulsa pada sore hari (15.30 ke atas).\n• Segera kocor perakaran dengan air bersih atau larutan starter perangsang akar (200 ml per lubang).",
                    'tahap_pemeliharaan' => "1. Pemasangan Ajir & Pengikatan:\n• Tancapkan ajir bambu setinggi 1.5 meter di samping lubang tanam saat tanaman berumur 7–10 HST.\n• Ikatkan batang cabe ke ajir dengan tali rafia membentuk angka 8 agar batang tidak terjepit.\n\n2. Pewiwitan Tunas Air (Rempel):\n• Rempel seluruh tunas air di ketiak daun yang tumbuh di bawah cabang Y utama saat cabe berumur 15–25 HST.\n• Sisakan bunga pertama pada cabang Y atau buang bunga pertama bila tanaman masih ingin dipacu percabangannya.\n\n3. Jadwal Pemupukan Susulan:\n• Vegetatif (10–35 HST): Kocor NPK 16-16-16 (5 gr/L) + Asam Humat interval 7 hari sekali.\n• Generatif (40 HST ke atas): Kurangi Nitrogen! Tingkatkan Fosfat, Kalium, dan Kalsium (MKP, KNO3 putih, Kalsium Boron) lewat kocor dan semprot foliar.",
                    'tips_penting' => "• Hindari penggunaan pupuk Urea berlebih saat fase pembungaan, karena kelebihan nitrogen menyebabkan bunga cabe rontok massal dan tanaman rentan serangan jamur antraknosa (patek).\n• Semprotkan kalsium foliar seminggu sekali di pagi hari untuk mempertebal dinding sel buah, sehingga buah cabe tahan busuk dan tidak mudah layu saat panen.\n• Rutin kendalikan hama pembawa virus (kutu kebul, thrips, dan tungau) dengan rotasi insektisida berbahan aktif berbeda.",
                    'hama_penyakit' => "1. Hama Utama & Pengendalian:\n• Thrips (Thrips parvispinus):\n  - Gejala: Daun keriting menggulung ke atas, permukaan bawah daun keperakan/tembaga, pucuk daun kaku mengecil.\n  - Solusi: Semprot insektisida bahan aktif Spinetoram atau Abamektin, rotasi dengan Fipronil atau Imidakloprid. Selalu gunakan perekat/penembus.\n• Tungau Merah / Kuning (Polyphagotarsonemus latus):\n  - Gejala: Daun melengkung terbalik ke bawah seperti sendok telungkup, daun menebal kaku dan kecoklatan.\n  - Solusi: Akarisida berbahan aktif Piridaben, Abamektin, atau Heksitiazoks. Semprot merata ke bawah daun.\n• Kutu Kebul (Bemisia tabaci):\n  - Gejala: Vektor virus Gemini (bule/kuning). Kutu putih berhamburan saat daun digoyang.\n  - Solusi: Insektisida sistemik Tiametoksem atau Dinotefuran, pasang yellow sticky trap (perangkap kuning).\n\n2. Penyakit Utama & Solusi:\n• Antraknosa / Patek (Colletotrichum capsici):\n  - Gejala: Bercak cekung melingkar pada buah cabe matang/muda seperti terbakar, terdapat titik spora hitam melingkar.\n  - Solusi: Pangkas buah terinfeksi dan buang jauh. Semprot fungisida bahan aktif Azoksistrobin + Difenokonazol bergantian dengan Klorotalonil atau Mankozeb. Tambahkan Kalsium Boron.\n• Layu Fusarium & Layu Bakteri:\n  - Gejala: Tanaman layu permanen dan mati cepat.\n  - Solusi: Jaga drainase bedengan agar tidak becek, kocor agen hayati Trichoderma & Bacillus subtilis, gunakan Bakterisida tembaga bila terserang bakteri.",
                ],
                'guides' => [
                    [
                        'phase' => 'Pemupukan Dasar',
                        'hst' => '0 HST (Sebelum Tanam)',
                        'focus' => 'Penyesuaian pH tanah (netral) & pondasi unsur makro',
                        'nutrients' => ['Kapur Pertanian (Dolomit)', 'Pupuk Kandang', 'SP-36', 'Za'],
                        'dosis' => 'Dolomit 100 gr/meter bedengan + pupuk kandang 1.5 kg/meter + SP-36 15 gr/lubang.',
                        'metode' => 'Tebar di bedengan, biarkan terkena hujan/disiram sebelum ditutup mulsa perak hitam.',
                        'tips' => 'Kadar pH 6.0 – 6.8 sangat penting agar penyerapan pupuk susulan tidak terikat racun tanah.',
                        'urutan' => 1,
                    ],
                ],
            ],

            [
                'catalog' => [
                    'key' => 'jagung',
                    'name' => 'Jagung',
                    'varietas' => 'Jagung Manis / Pipil (Bonanza F1, Secada, Talenta)',
                    'emoji' => '🌽',
                    'cycle' => '70 – 85 Hari',
                    'deskripsi' => 'Tanaman pangan dan hortikultura berakar serabut kuat. Membutuhkan asupan hara nitrogen tinggi serta penyinaran matahari penuh tanpa naungan.',
                    'theme' => 'amber',
                    'keywords' => 'jagung, corn, maize',
                    'urutan' => 3,
                    'aktif' => true,
                    'tahap_semai' => "1. Tanam Langsung (Direct Seeding):\n• Jagung umumnya ditanam langsung (direct seeding) di lahan bedengan tanpa proses semai baki tray agar akar tunggang awal tidak rusak atau tertekuk.\n\n2. Perlakuan Benih (Seed Dressing):\n• Campurkan benih jagung dengan insektisida/fungisida perlakuan benih (misal berbahan aktif metalaksil atau tiametoksam) sebelum ditanam.\n• Perlakuan benih melindungi biji jagung dari serangan semut, lalat bibit (Atherigona), dan infeksi penyakit bulai (Peronosclerospora).\n\n3. Uji Mutu Benih:\n• Pastikan kemasan benih hibrida bersertifikat dengan daya tumbuh di atas 85%.",
                    'tahap_penanaman' => "1. Pembuatan Lubang Tugal:\n• Buat lubang tanam menggunakan kayu tugal sedalam 3–5 cm di permukaan tanah yang telah gembur dan cukup lembap.\n\n2. Jarak Tanam Standar:\n• Jarak antar baris: 70–75 cm.\n• Jarak dalam baris: 20–25 cm (1 biji per lubang) atau 40 cm (2 biji per lubang).\n\n3. Penanaman & Penutupan:\n• Masukkan benih ke dalam lubang tugal, lalu tutup tipis dengan tanah gembur atau kompos matang.\n• Jangan menginjak atau memadatkan tanah di atas benih agar calon tunas jagung dapat menembus tanah dengan mudah.",
                    'tahap_pemeliharaan' => "1. Pembumbunan & Pendangiran:\n• Lakukan pembumbunan (menaikkan tanah ke pangkal batang jagung) pada umur 15–20 HST dan diulangi pada 35–40 HST.\n• Pembumbunan memperkokoh akar tunjang (akar jangkar) sehingga tanaman jagung tidak mudah roboh tertiup angin kencang.\n\n2. Penyiangan Gulma:\n• Bersihkan gulma di sekitar perakaran sebelum melakukan pemupukan susulan agar nutrisi pupuk diserap optimal oleh tanaman jagung.\n\n3. Pemupukan Bertahap:\n• Susulan 1 (15–20 HST): Tugal Urea 7 gr + NPK 5 gr per lubang dengan jarak 7–10 cm dari pangkal batang.\n• Susulan 2 (35–45 HST menjelang malai keluar): Tugal Urea 5 gr + NPK 10 gr + KCL 5 gr per batang, lalu siram.",
                    'tips_penting' => "• Fase serbuk sari keluar (silking & tasseling) sangat peka terhadap kekeringan. Pastikan pasokan air tanah cukup agar tongkol jagung terisi biji penuh dan tidak ompong.\n• Pada jagung manis, buang tongkol kedua (tongkol bawah) pada umur 45–50 HST jika ingin menghasilkan satu tongkol utama berukuran jumbo (grade super).\n• Waspadai ulat grayak frugiperda (FAW) pada pupus pucuk daun jagung sejak umur 10 HST.",
                    'hama_penyakit' => "1. Hama Utama & Pengendalian:\n• Ulat Grayak Jagung / FAW (Spodoptera frugiperda):\n  - Gejala: Pupus daun jagung robek tercabik-cabik, terdapat serbuk kotoran mirip serbuk gergaji di dalam corong daun.\n  - Solusi: Semprot insektisida berbahan aktif Emamektin Benzoat atau Klorantraniliprol langsung mengarah ke dalam corong/pupus tanaman jagung saat sore/malam hari.\n• Penggerek Tongkol (Helicoverpa armigera):\n  - Gejala: Ulat masuk merusak ujung tongkol dan memakan biji muda jagung.\n  - Solusi: Semprot insektisida saat fase silking (rambut jagung keluar) dengan Sipermetrin atau Metomil.\n• Lalat Bibit (Atherigona oryzae):\n  - Gejala: Tunas muda umur 7–14 HST menguning, layu, dan mudah dicabut (dead-heart).\n  - Solusi: Perlakuan benih (seed treatment) dengan Tiametoksam sebelum tanam, atau tabur insektisida karbofuran di dekat lubang tanam.\n\n2. Penyakit Utama & Solusi:\n• Penyakit Bulai (Peronosclerospora maydis):\n  - Gejala: Garis putih/kuning membujur sejajar tulang daun, daun klorotik, tanaman kerdil dan tidak bertongkol.\n  - Solusi: Perlakukan benih dengan fungisida Metalaksil atau Dimetomorf sebelum semai. Cabut dan bakar tanaman yang tergejala sedini mungkin agar spora tidak menyebar.\n• Karat Daun (Puccinia sorghi) & Hawar Daun (Helminthosporium):\n  - Gejala: Bisul coklat kemerahan seperti serbuk karat di daun jagung bawah.\n  - Solusi: Semprot fungisida Mankozeb atau Azoksistrobin saat gejala awal muncul.",
                ],
                'guides' => [
                    [
                        'phase' => 'Pemupukan Dasar',
                        'hst' => '0 HST (Saat Tanam)',
                        'focus' => 'Memacu kecambah cepat & perakaran tunggang yang kuat',
                        'nutrients' => ['NPK 15-15-15 atau SP-36', 'Kompos Organik'],
                        'dosis' => '5–7 gram per lubang yang ditugal 7 cm di samping lubang benih.',
                        'metode' => 'Tugal di samping biji dan tutup dengan tanah gembur tipis.',
                        'tips' => 'Jangan letakkan pupuk anorganik menempel langsung pada biji agar benih tidak terbakar.',
                        'urutan' => 1,
                    ],
                ],
            ],

            [
                'catalog' => [
                    'key' => 'tomat',
                    'name' => 'Tomat',
                    'varietas' => 'Tomat Sayur / Buah (Servo F1, Tymoti F1, Gustavi)',
                    'emoji' => '🍅',
                    'cycle' => '65 – 85 Hari',
                    'deskripsi' => 'Sayuran buah yang sangat populer. Memerlukan penopang ajir kuat, perempelan tunas liar berkala, serta aplikasi kalsium untuk mencegah pantat buah busuk hitam.',
                    'theme' => 'rose',
                    'keywords' => 'tomat, tomato, ranti',
                    'urutan' => 4,
                    'aktif' => true,
                    'tahap_semai' => "1. Perendaman Benih:\n• Rendam benih tomat dalam air hangat (40–45°C) selama 2–3 jam.\n• Tiriskan dan angin-anginkan benih hingga kering permukaan sebelum ditabur.\n\n2. Media Persemaian:\n• Gunakan media steril: tanah remah + kompos matang + cocopeat halus (1:1:1).\n• Masukkan ke dalam tray semai lubang 105 atau polybag kecil.\n• Buat lubang sedalam 0.5 cm, masukkan 1 benih per lubang, tutup tipis.\n\n3. Perawatan Semai:\n• Berikan sungkup plastik bening untuk melindungi bibit muda dari terpaan hujan langsung.\n• Siram tipis-tipis setiap pagi menggunakan semprotan kabut halus.\n• Bibit siap dipindah tanam pada umur 20–25 hari (berdaun sejati 4–5 helai).",
                    'tahap_penanaman' => "1. Bedengan & Lubang Tanam:\n• Buat bedengan lebar 100 cm bermulsa plastik perak-hitam.\n• Jarak tanam: 50 cm x 60 cm pola dua baris zig-zag.\n\n2. Penanaman:\n• Siram tray semai sebelum bibit dicabut agar perakaran tetap utuh.\n• Lubangi mulsa, masukkan bibit tegak lurus, tutup pangkal batang dengan tanah halus, dan segera kocor air bersih 200 ml per lubang.\n• Lakukan penanaman di sore hari agar bibit tidak layu tersengat terik matahari.",
                    'tahap_pemeliharaan' => "1. Pemasangan Ajir/Lanjaran:\n• Pasang ajir bambu setinggi 1.5–1.8 meter sedini mungkin (1–3 hari setelah tanam) agar tidak melukai akar yang mulai menjalar.\n• Ikatkan batang tanaman pada ajir secara teratur membentuk angka 8.\n\n2. Pemangkasan (Pruning) Tunas Air:\n• Buang seluruh tunas samping (wiwitan) yang tumbuh di ketiak daun, sisakan hanya 1 atau 2 cabang utama yang dipelihara.\n• Pangkas daun-daun tua di bagian bawah yang mulai menguning untuk melancarkan sirkulasi udara.\n\n3. Pemupukan Seimbang:\n• Fase awal: Kocor NPK seimbang 16-16-16 untuk memacu pertumbuhan vegetatif.\n• Fase bunga & buah: Tambahkan pupuk tinggi Kalium, Fosfat, dan Kalsium (MKP, KNO3, Kalsium Amonium Nitrat) untuk bobot buah padat dan berkulit tebal.",
                    'tips_penting' => "• Gejala Blossom End Rot (busuk hitam kering pada pantat buah tomat) disebabkan oleh kekurangan Kalsium dan fluktuasi kelembapan tanah yang drastis. Berikan kalsium secara teratur dan jaga kelembapan tanah tetap stabil.\n• Hindari menyiram tanaman tomat dengan semprotan air dari atas daun di sore hari, karena daun yang basah semalaman memicu spora jamur Phytophthora (busuk daun & busuk batang).",
                    'hama_penyakit' => "1. Hama Utama & Pengendalian:\n• Ulat Buah Tomat (Helicoverpa armigera):\n  - Gejala: Lubang pada buah tomat muda maupun masak, isi buah membusuk dan berair.\n  - Solusi: Semprot insektisida berbahan aktif Emamektin Benzoat, Flubendiamida, atau Klorantraniliprol saat pembentukan buah muda.\n• Lalat Pengorok Daun / Leaf Miner (Liriomyza sp.):\n  - Gejala: Alur liukan garis putih berliku di permukaan daun tomat akibat larva mengorok daging daun.\n  - Solusi: Semprot insektisida translaminar berbahan aktif Abamektin atau Siromazin.\n• Kutu Kebul (Vektor Virus Kuning / TYLCV):\n  - Gejala: Daun mengkerut kecil kekuningan, pucuk melingkar kerdil.\n  - Solusi: Semprot insektisida berbahan aktif Imidakloprid atau Tiametoksam, gunakan perangkap kuning lengket.\n\n2. Penyakit Utama & Solusi:\n• Busuk Daun / Hawar Daun (Phytophthora infestans):\n  - Gejala: Bercak basah kecoklatan pada daun, batang menghitam, dan buah busuk basah berbau khas saat musim hujan.\n  - Solusi: Semprot fungisida sistemik Simoksanil, Dimetomorf, atau Propamokarb hidroklorida dirotasi dengan Mankozeb/Tembaga hidroksida.\n• Blossom End Rot (Busuk Pantat Buah):\n  - Gejala: Bagian pantat buah tomat mengering, cekung hitam mendatar (bukan karena jamur, tapi defisiensi Kalsium).\n  - Solusi: Semprot pupuk Kalsium Nitrat / Kalsium Boron foliar secara teratur dan stabilkan penyiraman agar media tidak kekeringan ekstrem.",
                ],
                'guides' => [
                    [
                        'phase' => 'Pemupukan Dasar',
                        'hst' => '0 HST (Sebelum Tanam)',
                        'focus' => 'Pondasi awal struktur tanah & hara makro',
                        'nutrients' => ['Kompos Fermentasi', 'NPK 16-16-16', 'Dolomit'],
                        'dosis' => '1 kg kompos + 15 gr NPK per lubang tanam.',
                        'metode' => 'Campur rata di lubang tanam dan tutup tipis sebelum penanaman.',
                        'tips' => 'Pastikan pH tanah netral agar penyerapan pupuk maksimal.',
                        'urutan' => 1,
                    ],
                ],
            ],
        ];

        foreach ($plants as $data) {
            $catalog = PlantCatalog::updateOrCreate(
                ['key' => $data['catalog']['key']],
                $data['catalog'],
            );

            if (! empty($data['guides'])) {
                foreach ($data['guides'] as $guide) {
                    PlantCatalogGuide::updateOrCreate(
                        [
                            'plant_catalog_id' => $catalog->id,
                            'phase' => $guide['phase'],
                        ],
                        array_merge($guide, ['plant_catalog_id' => $catalog->id]),
                    );
                }
            }

            if (! empty($data['catalog']['hama_penyakit'])) {
                $parsedHamas = PlantCatalog::parseTextToCards($data['catalog']['hama_penyakit'], 'hama', $catalog->name);
                foreach ($parsedHamas as $idx => $hamaCard) {
                    PlantCatalogHama::updateOrCreate(
                        [
                            'plant_catalog_id' => $catalog->id,
                            'judul' => $hamaCard['title'],
                        ],
                        [
                            'kategori' => $hamaCard['category'] ?: 'Hama & Penyakit',
                            'gejala' => $hamaCard['gejala'] ?? null,
                            'solusi' => $hamaCard['solusi'] ?? null,
                            'urutan' => $idx + 1,
                        ],
                    );
                }
            }
        }
    }
}
