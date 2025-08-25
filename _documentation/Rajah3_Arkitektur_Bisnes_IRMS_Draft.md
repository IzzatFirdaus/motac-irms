# Rajah 3: Arkitektur Bisnes Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS) v4.0

```mermaid
flowchart TB
    %% Layer 1: Service Medium
    subgraph L1 MEDIUM PERKHIDMATAN
        M1[Portal Aplikasi]
    end

    %% Layer 2: Service Users (Internal)
    subgraph L2 PENGGUNA PERKHIDMATAN DALAMAN
        U1[Pemohon (Staf MOTAC)]
        U2[Pegawai Penyokong]
        U3[Staf BPM]
        U4[Agen IT]
    end

    %% Layer 3: Main Services
    subgraph L3 PERKHIDMATAN UTAMA
        S1[Perkhidmatan Pinjaman Peralatan ICT]
        S2[Perkhidmatan Meja Bantuan ICT]
    end

    %% Layer 4: Application System
    subgraph L4 SISTEM APLIKASI
        SYS1[Sistem Pengurusan Sumber Terintegrasi (IRMS) v4.0]
        MOD1[Modul Pinjaman ICT]
        MOD2[Modul Helpdesk]
        SYS1 --> MOD1
        SYS1 --> MOD2
    end

    %% Layer 5: Information (Data)
    subgraph L5 MAKLUMAT DATA
        D1[Maklumat Pengguna & Organisasi]
        D2[Inventori Aset]
        D3[Permohonan Pinjaman]
        D4[Tiket Helpdesk]
    end

    %% Layer 6: Governing Division
    subgraph L6 BAHAGIAN YANG DIKAWAL SELIA
        DIV1[Bahagian Pengurusan Maklumat (BPM)]
    end

    %% Layer 7: External Data Provider
    subgraph L7 AGENSi PEMBEKAL MAKLUMAT
        EXT1[Sistem HR Pusat (contoh)]
    end

    %% Layer 8: Technology
    subgraph L8 TEKNOLOGI
        T1[Laravel]
        T2[Livewire]
        T3[MySQL]
        T4[Docker]
    end

    %% Connections
    M1 --> U1
    M1 --> U2
    M1 --> U3
    M1 --> U4

    U1 --> S1
    U2 --> S1
    U3 --> S1
    U4 --> S2

    S1 --> MOD1
    S2 --> MOD2

    MOD1 --> D3
    MOD1 --> D2
    MOD2 --> D4

    D1 <-- MOD1
    D1 <-- MOD2

    D1 --> DIV1
    D2 --> DIV1
    D3 --> DIV1
    D4 --> DIV1

    DIV1 --> EXT1

    L8 --> SYS1

    %% Caption
    classDef caption fill:#fff,stroke:none,color:#222;
    C1["Rajah 3: Arkitektur Bisnes Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS) v4.0"]:::caption
```
