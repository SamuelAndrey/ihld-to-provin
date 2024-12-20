<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telkom | Update Provin Data</title>
    <link href="/static/bootstrap.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/static/style.css">
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
        }

        .loading-spinner {
            width: 80px;
            height: 80px;
            border: 10px solid #f3f3f3;
            border-top: 10px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: white;
            font-size: 18px;
            text-align: center;
            margin-top: 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

<div class="header">
    <a href="/">
        <img src="/static/telkom_logo_vertical.svg" alt="Telkom Indonesia Logo">
    </a>
</div>

<div class="form-container">

    <h1 class="title">Update provin data</h1>
    <p class="subtitle">Powered by PT. Telkom Infrastuktur Indonesia</p>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="processForm" action="{{ route('sync.import') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="data1" class="form-label">Data IHLD Terbaru ( Format Default No Edit! )</label>
            <input type="file" id="data1" name="ihld_data" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Process</button>
        <button type="button" class="btn btn-outline-light w-100" data-bs-toggle="modal" data-bs-target="#csvHelpModal">
            Petunjuk Format CSV
        </button>
    </form>

</div>

<div class="loading-overlay align-items-center" id="loadingOverlay">
    <div class="container text-center">
        <div class="row justify-content-center align-items-center">
            <div class="col-12">
                <div class="loading-spinner mx-auto"></div>
            </div>
            <div class="col-12">
                <div class="loading-text">Memproses data...<br>Mohon jangan menutup halaman ini.</div>
            </div>
            <div class="col-12 mt-4">
                <a href="/">
                    <button class="btn btn-danger">Batalkan</button>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="csvHelpModal" tabindex="-1" aria-labelledby="csvHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="csvHelpModalLabel">Petunjuk Format CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark">
                <p>Pastikan file CSV Anda memiliki format data berikut:</p>
                <ul>
                    <li>Cukup file original IHLD dari penarikan data</li>
                    <li>Tidak perlu diedit ulang</li>
                    <li>Cukup langung import</li>
                </ul>
{{--                <p>Contoh:</p>--}}
{{--                <img src="/static/csv_format.png" alt="Contoh Format CSV" class="img-fluid">--}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="/static/bootstrap.bundle.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('processForm');
        const overlay = document.getElementById('loadingOverlay');

        form.addEventListener('submit', function (e) {
            console.log("Form submitted, showing loading overlay...");
            if (overlay) {
                overlay.style.display = 'flex';
            }
        });
    });
</script>

</body>

</html>
