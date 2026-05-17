<?php

namespace App\Exports;

use App\Models\ListSparepartEng;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SparepartExport implements FromCollection, WithHeadings, WithDrawings
{
    public function collection()
    {
        // Tarik data field teks tanpa memanggil string nama image mentah
        return ListSparepartEng::select('id', 'name', 'category', 'length', 'width', 'thickness', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Sparepart Name', 'Category', 'Length', 'Width', 'Thickness', 'Registered At'];
    }

    // 🌟 LOGIKA INJECT GAMBAR KE ROW EXCEL
    public function drawings()
    {
        $drawings = [];
        $spareparts = ListSparepartEng::all();
        $row = 2; // Mulai dari baris kedua karena baris satu dipake heading

        foreach ($spareparts as $item) {
            if ($item->image && file_exists(storage_path('app/public/' . $item->image))) {
                $drawing = new Drawing();
                $drawing->setName($item->name);
                $drawing->setDescription($item->name);
                $drawing->setPath(storage_path('app/public/' . $item->image));
                $drawing->setHeight(40); // Atur tinggi gambar di Excel
                $drawing->setCoordinates('H' . $row); // Letakkan gambar di Kolom H baris saat ini
                $drawings[] = $drawing;
            }
            $row++;
        }

        return $drawings;
    }
}