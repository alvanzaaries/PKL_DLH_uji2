<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\ReconciliationDetail;
use App\Models\ReconciliationFact;
use App\Models\MappingAlias;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    public function index()
    {
        $data = Reconciliation::latest()->get();
        return view('reconciliations.index', compact('data'));
    }

    public function create()
    {
        return view('reconciliations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'quarter' => 'required|integer|min:1|max:4',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $sheet = IOFactory::load($file->getPathname())->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 5) {
                throw new \Exception('File tidak memiliki data yang cukup');
            }

            [$headerIndex, $colWilayah, $colJenis] = $this->detectHeader($rows);

            if ($headerIndex === null) {
                throw new \Exception('Header tidak terdeteksi');
            }

            $headers = $this->cleanHeaders($rows[$headerIndex]);
            $dataRows = array_slice($rows, $headerIndex + 1);

            $recon = Reconciliation::create([
                'year' => $request->year,
                'quarter' => $request->quarter,
                'original_filename' => $file->getClientOriginalName(),
            ]);

            foreach ($dataRows as $row) {
                if ($this->isEmptyRow($row)) continue;

                $mapped = $this->mapRowToHeaders($headers, $row);

                $detail = ReconciliationDetail::create([
                    'reconciliation_id' => $recon->id,
                    'wilayah' => $row[$colWilayah] ?? null,
                    'jenis_sdh' => $row[$colJenis] ?? null,
                    'raw_data' => json_encode($mapped),
                ]);

                $this->normalizeDetail($detail);
            }

            DB::commit();

            return redirect()->route('reconciliations.index')
                ->with('success', 'Upload berhasil. Data siap untuk validasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => $e->getMessage()]);
        }
    }

    public function destroy(Reconciliation $reconciliation)
    {
        $reconciliation->delete();
        return back()->with('success', 'Data dihapus');
    }

    private function detectHeader(array $rows)
    {
        $wilayahKeys = ['wilayah', 'provinsi', 'kabupaten', 'kota', 'daerah'];
        $jenisKeys = ['jenis', 'komoditas', 'sdh', 'hhk', 'hhbk'];

        for ($i = 0; $i < min(20, count($rows)); $i++) {
            $row = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$i]);

            $w = $this->findIndex($row, $wilayahKeys);
            $j = $this->findIndex($row, $jenisKeys);

            if ($w !== null && $j !== null) {
                return [$i, $w, $j];
            }
        }

        return [null, null, null];
    }

    private function findIndex(array $row, array $keys)
    {
        foreach ($row as $i => $cell) {
            foreach ($keys as $key) {
                if (str_contains($cell, $key)) {
                    return $i;
                }
            }
        }
        return null;
    }

    private function cleanHeaders(array $headers)
    {
        $result = [];
        foreach ($headers as $i => $h) {
            $result[$i] = $h ? trim($h) : "kolom_$i";
        }
        return $result;
    }

    private function isEmptyRow(array $row)
    {
        return count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0;
    }

    private function mapRowToHeaders(array $headers, array $row)
    {
        $result = [];
        foreach ($headers as $i => $h) {
            $result[$h] = $row[$i] ?? null;
        }
        return $result;
    }

    private function normalizeDetail(ReconciliationDetail $detail)
    {
        $wilayahId = $this->mapAlias('wilayah', $detail->wilayah);
        $komoditasId = $this->mapAlias('komoditas', $detail->jenis_sdh);

        $raw = json_decode($detail->raw_data, true);

        $volume = null;
        foreach ($raw as $k => $v) {
            if (str_contains(strtolower($k), 'volume')) {
                $volume = (float) str_replace(',', '.', $v);
            }
        }

        ReconciliationFact::create([
            'reconciliation_detail_id' => $detail->id,
            'wilayah_id' => $wilayahId,
            'komoditas_id' => $komoditasId,
            'volume' => $volume,
        ]);
    }

    private function mapAlias(string $type, ?string $value)
    {
        if (!$value) return null;

        $value = strtolower($value);

        $alias = MappingAlias::where('type', $type)
            ->whereRaw('? LIKE CONCAT("%", alias, "%")', [$value])
            ->first();

        return $alias?->master_id;
    }
}
