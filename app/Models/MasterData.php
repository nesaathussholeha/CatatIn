
<?php

class MasterData
{
    private $dataFile;

    private $allowedTabs = [
        'jurusan',
        'matakuliah',
        'kategori'
    ];

    public function __construct()
    {
        $dataDir = ROOT_PATH . '/storage/data';

        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0775, true);
        }

        $this->dataFile = $dataDir . '/master_data.json';

        if (!file_exists($this->dataFile)) {
            $this->initializeData();
        }
    }

    private function initializeData()
    {
        $data = [
            'jurusan' => [
                [
                    'id' => 'j1',
                    'kode' => 'TIF',
                    'nama' => 'Informatika',
                    'status' => true,
                    'pengguna' => '42 Catatan'
                ],
                [
                    'id' => 'j2',
                    'kode' => 'SI',
                    'nama' => 'Sistem Informasi',
                    'status' => true,
                    'pengguna' => '0 Catatan'
                ],
                [
                    'id' => 'j3',
                    'kode' => 'TEKOM',
                    'nama' => 'Teknik Komputer',
                    'status' => false,
                    'pengguna' => '12 Catatan'
                ]
            ],

            'matakuliah' => [
                [
                    'id' => 'm1',
                    'kode' => 'MK01',
                    'nama' => 'Pemrograman Web',
                    'jurusan' => 'Informatika',
                    'status' => true,
                    'pengguna' => '25 Catatan'
                ],
                [
                    'id' => 'm2',
                    'kode' => 'MK02',
                    'nama' => 'Basis Data',
                    'jurusan' => 'Sistem Informasi',
                    'status' => true,
                    'pengguna' => '0 Catatan'
                ]
            ],

            'kategori' => [
                [
                    'id' => 'k1',
                    'kode' => 'KAT01',
                    'nama' => 'Rangkuman Materi',
                    'status' => true,
                    'pengguna' => '50 Catatan'
                ],
                [
                    'id' => 'k2',
                    'kode' => 'KAT02',
                    'nama' => 'Latihan Soal',
                    'status' => false,
                    'pengguna' => '0 Catatan'
                ]
            ]
        ];

        $this->writeData($data);
    }

    private function readData()
    {
        $json = file_get_contents($this->dataFile);

        $data = json_decode($json, true);

        return is_array($data) ? $data : [
            'jurusan' => [],
            'matakuliah' => [],
            'kategori' => []
        ];
    }

    private function writeData($data)
    {
        file_put_contents(
            $this->dataFile,
            json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            LOCK_EX
        );
    }

    private function validTab($tab)
    {
        return in_array($tab, $this->allowedTabs, true);
    }

    public function getAll()
    {
        return $this->readData();
    }

    public function save($tab, $id, $kode, $nama, $extra = '')
    {
        if (!$this->validTab($tab)) {
            return false;
        }

        $data = $this->readData();

        if ($kode === '' || $nama === '') {
            return false;
        }

        $kode = strtoupper(trim($kode));
        $nama = trim($nama);

        if ($id === '') {
            $newItem = [
                'id' => uniqid(),
                'kode' => $kode,
                'nama' => $nama,
                'status' => true,
                'pengguna' => '0 Catatan'
            ];

            if ($tab === 'matakuliah') {
                $newItem['jurusan'] = trim($extra);
            }

            $data[$tab][] = $newItem;
        } else {
            foreach ($data[$tab] as &$item) {
                if ($item['id'] === $id) {
                    $item['kode'] = $kode;
                    $item['nama'] = $nama;

                    if ($tab === 'matakuliah') {
                        $item['jurusan'] = trim($extra);
                    }

                    break;
                }
            }

            unset($item);
        }

        $this->writeData($data);

        return true;
    }

    public function toggleStatus($tab, $id)
    {
        if (!$this->validTab($tab)) {
            return false;
        }

        $data = $this->readData();

        foreach ($data[$tab] as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = !$item['status'];
                $this->writeData($data);
                unset($item);
                return true;
            }
        }

        unset($item);

        return false;
    }

    public function delete($tab, $id)
    {
        if (!$this->validTab($tab)) {
            return false;
        }

        $data = $this->readData();

        foreach ($data[$tab] as $index => $item) {
            if ($item['id'] === $id) {
                $count = (int) preg_replace(
                    '/[^0-9]/',
                    '',
                    $item['pengguna'] ?? '0'
                );

                // Data tidak boleh dihapus jika masih digunakan
                if ($count > 0) {
                    return false;
                }

                array_splice($data[$tab], $index, 1);

                $this->writeData($data);

                return true;
            }
        }

        return false;
    }
}
