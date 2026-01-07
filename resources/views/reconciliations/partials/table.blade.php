<div class="overflow-x-auto border rounded-lg">
    <table class="min-w-full text-xs text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
            <tr>
                {{-- Helper untuk Link Sort --}}
                @php
                    $sortLink = function($col) use ($reconciliation) {
                        $dir = request('sort') == $col && request('direction') == 'asc' ? 'desc' : 'asc';
                        $params = request()->all();
                        $params['sort'] = $col;
                        $params['direction'] = $dir;
                        return route('reconciliations.show', array_merge(['reconciliation' => $reconciliation->id], $params));
                    };
                    $sortIcon = function($col) {
                        if (request('sort') != $col) return '<span class="text-gray-300 ml-1">↕</span>';
                        return request('direction') == 'asc' ? ' <span class="text-blue-600 ml-1">↑</span>' : ' <span class="text-blue-600 ml-1">↓</span>';
                    };
                @endphp

                <th class="px-3 py-3 whitespace-nowrap sticky left-0 bg-gray-100 z-10 shadow-sm min-w-[60px]">
                    <a href="{{ $sortLink('no_urut') }}" class="sort-link flex items-center space-x-1 hover:text-black group">
                        <span>No</span> {!! $sortIcon('no_urut') !!}
                    </a>
                </th>

                <th class="px-3 py-3 whitespace-nowrap min-w-[200px]">
                    <a href="{{ $sortLink('wilayah') }}" class="sort-link flex items-center space-x-1 hover:text-black group">
                        <span>Wilayah</span> {!! $sortIcon('wilayah') !!}
                    </a>
                </th>
                
                {{-- LHP Group --}}
                <th class="px-3 py-3 whitespace-nowrap bg-blue-50 border-l border-blue-100 text-blue-800 min-w-[120px]">
                    <a href="{{ $sortLink('lhp_no') }}" class="sort-link flex items-center space-x-1 hover:text-blue-900 group">
                        <span>LHP No</span> {!! $sortIcon('lhp_no') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-blue-800 min-w-[100px]">
                    <a href="{{ $sortLink('lhp_tanggal') }}" class="sort-link flex items-center space-x-1 hover:text-blue-900 group">
                        <span>Tgl LHP</span> {!! $sortIcon('lhp_tanggal') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-blue-800 min-w-[150px]">
                    <a href="{{ $sortLink('jenis_sdh') }}" class="sort-link flex items-center space-x-1 hover:text-blue-900 group">
                        <span>Jenis HH</span> {!! $sortIcon('jenis_sdh') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-right text-blue-800">
                    <a href="{{ $sortLink('volume') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-blue-900 group">
                        <span>Volume</span> {!! $sortIcon('volume') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-center text-blue-800 w-[60px]">
                    <a href="{{ $sortLink('satuan') }}" class="sort-link flex items-center justify-center space-x-1 hover:text-blue-900 group">
                        <span>Sat</span> {!! $sortIcon('satuan') !!}
                    </a>
                </th> 
                <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-right text-blue-800">
                    <a href="{{ $sortLink('lhp_nilai') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-blue-900 group">
                        <span>Nilai LHP (Rp)</span> {!! $sortIcon('lhp_nilai') !!}
                    </a>
                </th>
                
                {{-- Billing Group --}}
                <th class="px-3 py-3 whitespace-nowrap bg-green-50 border-l border-green-100 text-green-800 min-w-[120px]">
                    <a href="{{ $sortLink('billing_no') }}" class="sort-link flex items-center space-x-1 hover:text-green-900 group">
                        <span>Billing No</span> {!! $sortIcon('billing_no') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-green-50 text-green-800 min-w-[100px]">
                    <a href="{{ $sortLink('billing_tanggal') }}" class="sort-link flex items-center space-x-1 hover:text-green-900 group">
                        <span>Tgl Billing</span> {!! $sortIcon('billing_tanggal') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-green-50 text-right text-green-800">
                    <a href="{{ $sortLink('billing_nilai') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-green-900 group">
                        <span>Nilai Billing</span> {!! $sortIcon('billing_nilai') !!}
                    </a>
                </th>
                
                {{-- Setor Group --}}
                <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 border-l border-yellow-100 text-yellow-800 min-w-[100px]">
                    <a href="{{ $sortLink('setor_tanggal') }}" class="sort-link flex items-center space-x-1 hover:text-yellow-900 group">
                        <span>Tgl Setor</span> {!! $sortIcon('setor_tanggal') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 text-yellow-800 min-w-[120px]">
                    <a href="{{ $sortLink('setor_bank') }}" class="sort-link flex items-center space-x-1 hover:text-yellow-900 group">
                        <span>Bank</span> {!! $sortIcon('setor_bank') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 text-yellow-800 min-w-[120px]">
                    <a href="{{ $sortLink('setor_ntpn') }}" class="sort-link flex items-center space-x-1 hover:text-yellow-900 group">
                        <span>NTPN</span> {!! $sortIcon('setor_ntpn') !!}
                    </a>
                </th>
                <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 text-right text-yellow-800">
                    <a href="{{ $sortLink('setor_nilai') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-yellow-900 group">
                        <span>Nilai Setor</span> {!! $sortIcon('setor_nilai') !!}
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($details as $detail)
            <tr class="bg-white hover:bg-gray-50 transition-colors">
                <td class="px-3 py-2 sticky left-0 bg-white hover:bg-gray-50 font-medium text-center shadow-sm">{{ $detail->no_urut ?? '-' }}</td>
                <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">{{ $detail->wilayah }}</td>
                
                <td class="px-3 py-2 bg-blue-50/20 border-l border-blue-50 whitespace-nowrap">{{ $detail->lhp_no }}</td>
                <td class="px-3 py-2 bg-blue-50/20 whitespace-nowrap">{{ $detail->lhp_tanggal }}</td>
                <td class="px-3 py-2 bg-blue-50/20 whitespace-nowrap font-medium text-blue-600">{{ $detail->jenis_sdh }}</td>
                <td class="px-3 py-2 bg-blue-50/20 text-right font-bold text-gray-700">
                    {{ number_format($detail->volume, 2, ',', '.') }}
                </td>
                <td class="px-3 py-2 bg-blue-50/20 text-center text-xs font-bold text-gray-500">
                    {{ $detail->satuan }} 
                </td>
                <td class="px-3 py-2 bg-blue-50/20 text-right">
                    {{ number_format($detail->lhp_nilai, 0, ',', '.') }}
                </td>
                
                <td class="px-3 py-2 bg-green-50/20 border-l border-green-50 whitespace-nowrap">{{ $detail->billing_no }}</td>
                <td class="px-3 py-2 bg-green-50/20 whitespace-nowrap">{{ $detail->billing_tanggal }}</td>
                <td class="px-3 py-2 bg-green-50/20 text-right">
                    {{ number_format($detail->billing_nilai, 0, ',', '.') }}
                </td>
                
                <td class="px-3 py-2 bg-yellow-50/20 border-l border-yellow-50 whitespace-nowrap">{{ $detail->setor_tanggal }}</td>
                <td class="px-3 py-2 bg-yellow-50/20 whitespace-nowrap">{{ $detail->setor_bank }}</td>
                <td class="px-3 py-2 bg-yellow-50/20 whitespace-nowrap text-[10px] font-mono">{{ $detail->setor_ntpn }}</td>
                <td class="px-3 py-2 bg-yellow-50/20 text-right font-medium">
                    {{ number_format($detail->setor_nilai, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="15" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data yang cocok dengan pencarian Anda.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-4 border-t border-gray-200 bg-gray-50">
    {{ $details->links() }}
</div>