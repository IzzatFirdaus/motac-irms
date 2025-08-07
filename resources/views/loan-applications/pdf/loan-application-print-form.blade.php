{{-- resources/views/loan-applications/pdf/loan-application-print-form.blade.php --}}
{{--
    Printable PDF form for ICT loan application.
    This file has been renamed to match new naming conventions.
    All code structure and logic remain unchanged, only comments have been updated for documentation and clarity.
    This form is styled for exact matching to the official government form
--}}

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Borang Pinjaman ICT #{{ $loanApplication->id }}</title>
    <style>
        /* Styles to match official government form layout for A4 portrait and landscape pages */
        @page {
            margin: 35px;
            size: A4 portrait;
        }
        @page landscape {
            size: A4 landscape;
        }
        .landscape-page { page: landscape; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #000;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .form-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: left;
        }
        .section-title {
            background-color: #E7E6E6;
            font-weight: bold;
            text-align: left;
            font-size: 9pt;
        }
        .label {
            font-weight: normal;
            font-size: 9pt;
        }
        .value {
            font-weight: bold;
            font-size: 9pt;
        }
        .checkbox {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
        }
        .footer {
            font-size: 7pt;
            text-align: center;
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            width: 100%;
        }
        .signature-box {
            height: 45px;
            margin-top: 5px;
            border-bottom: 1px solid black;
        }
        .no-border, .no-border td, .no-border tr {
            border: none !important;
        }
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .inner-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        .accessories-table {
            width: 100%;
            font-size: 8pt;
        }
        .accessories-table td {
            padding: 1px;
        }
    </style>
</head>

<body>
    @php
        // Fetch related transactions and approvals for use in the form
        $issueTransaction = $loanApplication->loanTransactions->where('type', 'issue')->first();
        $returnTransaction = $loanApplication->loanTransactions->where('type', 'return')->first();
        $approval = $loanApplication->approvals->first();
    @endphp

    {{-- PAGE 1: Applicant and equipment info --}}
    <div style="page-break-after: always;">
        <table class="no-border" style="width: 100%; margin-bottom: 5px;">
            <tr>
                <td style="width: 20%; text-align: left; vertical-align: top;">
                    <img src="{{ public_path('assets/img/logo/logo_bpm_greyscale.png') }}" alt="Logo" style="width: 120px;">
                    <div style="font-size: 8pt; text-align:center;">Bahagian<br>Pengurusan Maklumat</div>
                </td>
                <td style="width: 60%; text-align: left; vertical-align: top; padding-top: 10px; padding-left: 10px;">
                    <div class="form-title">BORANG PERMOHONAN PEMINJAMAN PERALATAN ICT UNTUK KEGUNAAN RASMI</div>
                </td>
                <td style="width: 20%; text-align: right; vertical-align: top;">
                    <div style="font-size:8pt; margin-bottom: 2px;">PK.(S).MOTAC.07.(L3)</div>
                    <div style="border: 1px solid black; padding: 5px; display: inline-block; text-align: center;">
                        <strong>BORANG</strong><br>
                        <strong style="font-size: 24pt;">C</strong>
                    </div>
                </td>
            </tr>
        </table>
        <table class="main-table">
            <tr><td style="padding: 8px;"><span class="label">TARIKH BORANG PERMOHONAN LENGKAP DITERIMA</span></td></tr>
        </table>
        <br>
        <table class="main-table">
            <tr><td colspan="4" class="section-title">BAHAGIAN 1 | MAKLUMAT PEMOHON. Tanda * WAJIB diisi.</td></tr>
            <tr>
                <td class="label" style="width:25%;">Nama Penuh*</td>
                <td class="value">{{ $loanApplication->user->name ?? '' }}</td>
                <td class="label" style="width:20%;">No.Telefon*</td>
                <td class="value">{{ $loanApplication->applicant_phone ?? ($loanApplication->user->mobile_number ?? '') }}</td>
            </tr>
            <tr><td class="label">Jawatan & Gred*</td><td colspan="3" class="value">{{ $loanApplication->user->position?->name ?? '' }} ({{ $loanApplication->user->grade?->name ?? '' }})</td></tr>
            <tr><td class="label">Bahagian/Unit*</td><td colspan="3" class="value">{{ $loanApplication->user->department?->name ?? '' }}</td></tr>
            <tr><td class="label">Tujuan Permohonan*</td><td colspan="3" class="value" style="height: 30px;">{{ $loanApplication->purpose ?? '' }}</td></tr>
            <tr><td class="label">Lokasi*</td><td colspan="3" class="value">{{ $loanApplication->location ?? '' }}</td></tr>
            <tr>
                <td class="label">Tarikh Pinjaman*</td><td class="value">{{ $loanApplication->loan_start_date?->format('d/m/Y') ?? '' }}</td>
                <td class="label">Tarikh Dijangka Pulang*</td><td class="value">{{ $loanApplication->loan_end_date?->format('d/m/Y') ?? '' }}</td>
            </tr>
            <tr><td colspan="4" class="section-title">BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB. Tanda * WAJIB diisi.</td></tr>
            <tr>
                <td colspan="4">
                    <span class="checkbox">@if($loanApplication->user_id === $loanApplication->responsible_officer_id) &#9746; @else &#9744; @endif</span>
                    <span class="label">Sila tandakan ✓ jika Pemohon adalah Pegawai Bertanggungjawab. Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.</span>
                </td>
            </tr>
            <tr><td class="label">Nama Penuh*</td><td colspan="3" class="value">{{ $loanApplication->user_id !== $loanApplication->responsible_officer_id ? ($loanApplication->responsibleOfficer->name ?? '') : '-' }}</td></tr>
            <tr>
                <td class="label">Jawatan & Gred*</td><td class="value">{{ $loanApplication->user_id !== $loanApplication->responsible_officer_id ? (($loanApplication->responsibleOfficer->position?->name ?? '') . ' (' . ($loanApplication->responsibleOfficer->grade?->name ?? '') . ')') : '-' }}</td>
                <td class="label">No.Telefon*</td><td class="value">{{ $loanApplication->user_id !== $loanApplication->responsible_officer_id ? ($loanApplication->responsibleOfficer->mobile_number ?? '') : '-' }}</td>
            </tr>
            <tr><td colspan="4" class="section-title">BAHAGIAN 3 | MAKLUMAT PERALATAN</td></tr>
            <tr>
                <td class="label" style="text-align: left; width: 5%;">Bil.</td>
                <td class="label" style="text-align: left;">Jenis Peralatan</td>
                <td class="label" style="text-align: left; width: 15%;">Kuantiti</td>
                <td class="label" style="text-align: left;">Catatan</td>
            </tr>
            @for ($i = 0; $i < 4; $i++)
                <tr>
                    @if (isset($loanApplication->loanApplicationItems[$i]))
                        @php $item = $loanApplication->loanApplicationItems[$i]; @endphp
                        <td style="text-align:center;">{{ $i + 1 }}</td><td>{{ $item->equipment_type_name }}</td><td style="text-align:center;">{{ $item->quantity_requested }}</td><td>{{ $item->notes ?? '-' }}</td>
                    @else
                        <td style="height: 20px;">&nbsp;</td><td></td><td></td><td></td>
                    @endif
                </tr>
            @endfor
            <tr><td colspan="4" class="section-title">BAHAGIAN 4 | PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB)</td></tr>
            <tr><td colspan="4" class="label" style="font-size: 8pt; line-height: 1.2;">Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut;</td></tr>
            <tr>
                <td colspan="2" style="vertical-align: top;"><span class="label">Tarikh:</span> <span class="value">{{ $loanApplication->applicant_confirmation_timestamp?->format('d/m/Y') ?? '' }}</span></td>
                <td colspan="2" style="vertical-align: bottom;">
                    <div class="signature-box">&nbsp;</div><span class="label">Tandatangan & Cop (jika ada):</span><br><span class="label">Nama:</span> <span class="value">{{ $loanApplication->responsibleOfficer->name ?? $loanApplication->user->name }}</span>
                </td>
            </tr>
        </table>
        <div class="footer">No. Dokumen: PK.(S).KPK.08.(L3) Pin.1 | Tarikh Kuatkuasa: 1/1/2024 | Muka Surat: 1 daripada 4</div>
    </div>

    {{-- PAGE 2: Supporting officer approval and BPM/return info --}}
    <div style="page-break-after: always;">
        <div style="text-align: right; font-size:8pt;">PK.(S).MOTAC.07.(L3)</div>
        <table class="main-table">
            <tr><td colspan="2" class="section-title">BAHAGIAN 5 | PENGESAHAN BAHAGIAN / UNIT / SEKSYEN</td></tr>
            <tr><td colspan="2" class="label" style="font-size: 8pt;">Permohonan yang lengkap diisi oleh pemohon hendaklah DISOKONG OLEH PEGAWAI SEKURANG-KURANGNYA GRED 41 DAN KE ATAS.</td></tr>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <p><span class="label">Permohonan ini adalah: *</span>
                        <span class="checkbox">@if($approval?->status === 'approved') &#9746; @else &#9744; @endif</span> DISOKONG /
                        <span class="checkbox">@if($approval?->status === 'rejected') &#9746; @else &#9744; @endif</span> TIDAK DISOKONG
                    </p>
                    <span class="label">Tarikh:</span> <span class="value">{{ $approval?->approval_timestamp?->format('d/m/Y') ?? '' }}</span>
                </td>
                <td style="width: 50%; vertical-align: bottom;">
                    <div class="signature-box">&nbsp;</div>
                     <span class="label">Tandatangan & Cop (jika ada):</span><br>
                     <span class="label">Nama:</span> <span class="value">{{ $approval?->officer?->name ?? '' }}</span>
                </td>
            </tr>
            <tr><td colspan="2" class="section-title" style="text-align: center;">KEGUNAAN BAHAGIAN PENGURUSAN MAKLUMAT SAHAJA</td></tr>
            <tr>
                <td class="no-border" style="width: 50%; padding:0; vertical-align: top; border-right: 1px solid black !important;">
                    <table class="inner-table">
                        <tr><td class="section-title">BAHAGIAN 6 | SEMASA PEMINJAMAN</td></tr>
                        <tr><td>
                            <p class="label" style="margin: 0; font-weight: bold;">PEGAWAI PENGELUAR</p>
                            <div class="signature-box">&nbsp;</div><span class="label">Tandatangan & Cop (jika ada):</span><br>
                            <span class="label">Nama Penuh:</span><span class="value"> {{ $issueTransaction?->issuingOfficer?->name ?? '' }}</span> <br>
                            <span class="label">Bahagian/Unit:</span><span class="value"> BPM</span> <br>
                            <span class="label">Tarikh:</span><span class="value"> {{ $issueTransaction?->transaction_date?->format('d/m/Y') ?? '' }}</span>
                        </td></tr>
                        <tr><td>
                            <p class="label" style="margin: 0; font-weight: bold;">PEGAWAI PENERIMA</p>
                            <div class="signature-box">&nbsp;</div><span class="label">Tandatangan & Cop (jika ada):</span><br>
                            <span class="label">Nama Penuh:</span><span class="value"> {{ $issueTransaction?->receivingOfficer?->name ?? '' }}</span> <br>
                            <span class="label">Bahagian/Unit:</span><span class="value"> {{ $issueTransaction?->receivingOfficer?->department?->name ?? '' }}</span> <br>
                            <span class="label">Tarikh:</span><span class="value"> {{ $issueTransaction?->transaction_date?->format('d/m/Y') ?? '' }}</span>
                        </td></tr>
                    </table>
                </td>
                <td class="no-border" style="width: 50%; padding:0; vertical-align: top;">
                    <table class="inner-table">
                        <tr><td class="section-title">BAHAGIAN 7 | SEMASA PEMULANGAN</td></tr>
                        <tr><td>
                            <p class="label" style="margin: 0; font-weight: bold;">PEGAWAI YANG MEMULANGKAN</p>
                            <div class="signature-box">&nbsp;</div><span class="label">Tandatangan & Cop (jika ada):</span><br>
                            <span class="label">Nama Penuh:</span><span class="value"> {{ $returnTransaction?->returningOfficer?->name ?? '' }}</span> <br>
                            <span class="label">Bahagian/Unit:</span><span class="value"> {{ $returnTransaction?->returningOfficer?->department?->name ?? '' }}</span> <br>
                            <span class="label">Tarikh:</span><span class="value"> {{ $returnTransaction?->transaction_date?->format('d/m/Y') ?? '' }}</span>
                        </td></tr>
                        <tr><td>
                            <p class="label" style="margin: 0; font-weight: bold;">PEGAWAI TERIMA PULANGAN</p>
                            <div class="signature-box">&nbsp;</div><span class="label">Tandatangan & Cop (jika ada):</span><br>
                            <span class="label">Nama Penuh:</span><span class="value"> {{ $returnTransaction?->returnAcceptingOfficer?->name ?? '' }}</span> <br>
                            <span class="label">Bahagian/Unit:</span><span class="value"> BPM</span> <br>
                            <span class="label">Tarikh:</span><span class="value"> {{ $returnTransaction?->transaction_date?->format('d/m/Y') ?? '' }}</span>
                        </td></tr>
                    </table>
                </td>
            </tr>
            <tr><td colspan="2" style="height: 100px;"><span class="label">Catatan (jika ada):</span><span class="value"> {{ $returnTransaction?->return_notes ?? '-' }}</span></td></tr>
        </table>
        <div class="footer">No. Dokumen: PK.(S).KPK.08.(L3) Pin.1 | Tarikh Kuatkuasa: 1/1/2024 | Muka Surat: 2 daripada 4</div>
    </div>

    {{-- PAGE 3: Equipment issue details --}}
    <div class="landscape-page" style="page-break-after: always;">
        <hr style="border: 1px solid black; margin-bottom: 2px;">
        <div style="text-align: center; font-size: 9pt; font-weight: bold;">KEGUNAAN BAHAGIAN PENGURUSAN MAKLUMAT</div>
        <hr style="border: 1px solid black; margin-top: 2px; margin-bottom: 5px;">
        <table class="main-table">
            <tr><td colspan="5" class="section-title">BAHAGIAN 8 | MAKLUMAT PEMINJAMAN</td></tr>
            <tr>
                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 5%;">BIL.</td>
                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 20%;">JENIS PERALATAN</td>
                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 25%;">JENAMA DAN MODEL</td>
                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 20%;">NO. SIRI / TAG ID</td>
                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 30%;">AKSESORI</td>
            </tr>
             @for($i = 0; $i < 6; $i++)
                <tr>
                    @if($issueTransaction && isset($issueTransaction->loanTransactionItems[$i]))
                        @php $txItem = $issueTransaction->loanTransactionItems[$i]; @endphp
                        <td style="text-align: center;">{{ $i + 1 }}</td><td>{{ $txItem->loanApplicationItem->equipment_type_name }}</td>
                        <td>{{ $txItem->equipment->brand ?? '' }} {{ $txItem->equipment->model ?? '' }}</td><td>S/N: {{ $txItem->equipment->serial_number ?? 'N/A' }} <br> Tag: {{ $txItem->equipment->tag_id ?? 'N/A' }}</td>
                        <td>
                            @php
                                $checklistData = json_decode($issueTransaction->accessories_checklist_on_issue, true) ?? [];
                                $checklist = $checklistData['accessories'] ?? [];
                                $notes = $checklistData['notes'] ?? '';
                            @endphp
                             <table class="no-border accessories-table">
                                <tr>
                                    <td><span class="checkbox">@if(in_array('Power Adapter', $checklist))&#9746;@else&#9744;@endif</span> Power Adapter</td>
                                    <td><span class="checkbox">@if(in_array('Bag', $checklist))&#9746;@else&#9744;@endif</span> Beg</td>
                                    <td><span class="checkbox">@if(in_array('Mouse', $checklist))&#9746;@else&#9744;@endif</span> Mouse</td>
                                </tr>
                                <tr>
                                    <td><span class="checkbox">@if(in_array('Kabel USB', $checklist))&#9746;@else&#9744;@endif</span> Kabel USB</td>
                                    <td><span class="checkbox">@if(in_array('Kabel HDMI/VGA', $checklist))&#9746;@else&#9744;@endif</span> Kabel HDMI/VGA</td>
                                    <td><span class="checkbox">@if(in_array('Remote', $checklist))&#9746;@else&#9744;@endif</span> Remote</td>
                                </tr>
                                <tr><td colspan="3" style="padding-top: 5px;">Lain-lain. Nyatakan: <span class="value">{{$notes}}</span></td></tr>
                            </table>
                        </td>
                    @else
                        <td style="height: 60px;">&nbsp;</td><td></td><td></td><td></td><td></td>
                    @endif
                </tr>
            @endfor
        </table>
        <div class="footer">No. Dokumen: PK.(S).KPK.08.(L3) Pin.1 | Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 3 daripada 4</div>
    </div>

    {{-- PAGE 4: Terms and conditions --}}
    <div>
        <div style="text-align: right; font-size:8pt; margin-bottom: 10px;">PK.(S).MOTAC.07.(L3)</div>
        <h4 style="text-align:center; font-weight: bold; margin-bottom: 5px;">SYARAT-SYARAT PERMOHONAN PEMINJAMAN PERALATAN ICT UNTUK KEGUNAAN RASMI</h4>
        <h4 style="text-align:center; font-weight: bold;">KEMENTERIAN PELANCONGAN, SENI DAN BUDAYA</h4>
        <p style="font-weight: bold;">Peringatan:</p>
        <ol style="font-size: 10pt; list-style-position: outside; padding-left: 20px;">
            <li style="padding-left: 10px; margin-bottom: 5px;">Sila isi borang ini dengan lengkap. Tanda * adalah WAJIB diisi.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Permohonan adalah tertakluk kepada ketersediaan peralatan melalui konsep ‘First Come, First Serve’. Permohonan akan diteliti dan diuruskan dalam tempoh tiga (3) hari bekerja dari tarikh permohonan lengkap diterima. BPM tidak bertanggungjawab di atas ketersediaan peralatan jika pemohon gagal mematuhi tempoh ini.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pemohon hendaklah mengemukakan Borang Permohonan Pinjaman Peralatan ICT yang lengkap diisi dan ditandatangani kepada BPM semasa mengambil peralatan.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pemohon diingatkan untuk menyemak dan memeriksa kesempurnaan peralatan semasa mengambil dan sebelum memulangkan peralatan yang dipinjam. Kehilangan dan kekurangan pada peralatan semasa pemulangan adalah dibawah tanggungjawab pemohon dan tindakan melalui peraturan-peraturan yang berkuatkuasa boleh diambil.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pemohon merujuk kepada kakitangan yang melengkapkan borang permohonan peminjaman peralatan ICT.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pegawai Bertanggungjawab merujuk kepada kakitangan yang bertanggungjawab ke atas penggunaan, keselamatan dan kerosakan perlatan pinjaman.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pegawai Pengeluar merujuk kepada kakitangan BPM yang mengeluarkan peralatan untuk diberikan kepada Pegawai Penerima.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pegawai Penerima merujuk kepada kakitangan yang menerima peralatan daripada Pegawai Pengeluar.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pegawai Yang Memulangkan merujuk kepada kakitangan yang memulangkan peralatan yang dipinjam.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Pegawai Terima Pulangan merujuk kepada kakitangan BPM yang menerima peralatan yang dipulangkan oleh Pegawai Yang Memulangkan.</li>
            <li style="padding-left: 10px; margin-bottom: 5px;">Borang yang telah lengkap diisi hendaklah dihantar kepada:<br><br>
                <strong>Bahagian Pengurusan Maklumat</strong><br>
                <strong>KEMENTERIAN PELANCONGAN, SENI DAN BUDAYA</strong><br><br>
                Sebarang pertanyaan sila hubungi:<br><br>
                <strong>Unit Operasi Rangkaian dan Khidmat Pengguna</strong><br>
                <strong>Bahagian Pengurusan Maklumat</strong>
            </li>
        </ol>
        <div class="footer">No. Dokumen: PK.(S).KPK.08.(L3) Pin.1 | Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 4 daripada 4</div>
    </div>

    {{-- PDF page script for page numbering --}}
    <script type="text/php">
        // This script adds page numbers to the footer of each page.
        if (isset($pdf)) {
            $pdf->page_script(function($pageNumber, $pageCount, $canvas, $fontMetrics) {
                 $text = "Muka Surat: ".$pageNumber." daripada 4";
                $size = 7;
                $font = $fontMetrics->getFont("DejaVu Sans");
                $width = $fontMetrics->get_text_width($text, $font, $size);
                $x = ($canvas->get_width() - $width) / 2;
                $y = $canvas->get_height() - 30;
                $canvas->text($x, $y, $text, $font, $size, [0,0,0]);
            });
        }
    </script>
</body>
</html>
