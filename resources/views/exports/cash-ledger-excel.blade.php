<table>
    <tr>
        <td colspan="8" style="font-weight: bold; font-size: 14px;">{{ $companyName }}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold; font-size: 12px;">{{ $title }}</td>
    </tr>
    <tr>
        <td colspan="8">{{ $periodLabel }}</td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>

    <tr>
        <th>Tanggal</th>
        <th>Kategori</th>
        <th>Keterangan</th>
        <th>Debit</th>
        <th>Kredit</th>
        <th>Saldo</th>
        <th>Input oleh</th>
    </tr>

    <tr>
        <td colspan="5" style="text-align: center; color: #666;">Saldo awal</td>
        <td style="text-align: right;">{{ $openingBalance }}</td>
        <td style="text-align: center; color: #666;">-</td>
    </tr>

    @foreach($rows as $row)
        <tr>
            <td>{{ $row['occurred_at'] }}</td>
            <td>{{ $row['category'] }}</td>
            <td>{{ $row['description'] }}</td>
            <td style="text-align: right;">{{ $row['debit'] > 0 ? $row['debit'] : '' }}</td>
            <td style="text-align: right;">{{ $row['credit'] > 0 ? $row['credit'] : '' }}</td>
            <td style="text-align: right;">{{ $row['balance'] }}</td>
            <td>{{ $row['input_by'] }}</td>
        </tr>
    @endforeach
</table>
