<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview - {{ config('app.name', 'Salvation Admin') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Apple Touch Icon using church logo -->
    <link rel="apple-touch-icon" href="{{ asset('church-logo.webp') }}">

    <!-- Theme color for browser chrome -->
    <meta name="theme-color" content="#1e40af">

    <!-- Meta tags for better presentation -->
    <meta name="description" content="Certificate Preview - {{ config('app.name', 'Salvation Admin') }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- Open Graph tags -->
    <meta property="og:title" content="Certificate Preview">
    <meta property="og:description" content="Certificate document preview">
    <meta property="og:type" content="document">
    <meta property="og:image" content="{{ asset('church-logo.webp') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .viewer-container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .toolbar {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .toolbar h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }
        .toolbar-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .pdf-container {
            flex: 1;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow: auto;
        }
        .pdf-embed {
            width: 100%;
            max-width: 900px;
            height: calc(100vh - 120px);
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .error {
            text-align: center;
            padding: 40px;
            color: #e74c3c;
            background-color: #fdf2f2;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin: 20px;
        }
        @media (max-width: 768px) {
            .toolbar {
                padding: 10px;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            .toolbar-actions {
                justify-content: center;
            }
            .pdf-container {
                padding: 10px;
            }
            .pdf-embed {
                height: calc(100vh - 140px);
            }
        }
    </style>
</head>
<body>
    <div class="viewer-container">
        <div class="toolbar">
            <h1>Certificate Preview - {{ $filename ?? 'Preview' }}</h1>
            <div class="toolbar-actions">
                <a href="#" onclick="downloadPDF()" class="btn btn-primary">
                    📥 Download PDF
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    ← Back
                </a>
            </div>
        </div>

        <div class="pdf-container">
            @if(isset($pdfData) && $pdfData)
                <embed
                    src="data:application/pdf;base64,{{ $pdfData }}"
                    type="application/pdf"
                    class="pdf-embed"
                    title="Certificate Preview"
                >
            @else
                <div class="error">
                    <h3>PDF Generation Error</h3>
                    <p>Unable to generate PDF preview. Please try again.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function downloadPDF() {
            @if(isset($pdfData) && $pdfData)
                const pdfData = "{{ $pdfData }}";
                const filename = "{{ $filename ?? 'certificate-preview.pdf' }}";

                // Create a blob from the base64 data
                const byteCharacters = atob(pdfData);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: 'application/pdf' });

                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            @else
                alert('No PDF data available for download.');
            @endif
        }

        // Handle potential PDF loading errors
        document.addEventListener('DOMContentLoaded', function() {
            const embed = document.querySelector('.pdf-embed');
            if (embed) {
                embed.addEventListener('error', function() {
                    this.style.display = 'none';
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error';
                    errorDiv.innerHTML = `
                        <h3>PDF Display Error</h3>
                        <p>Your browser cannot display PDF files inline. Please download the file to view it.</p>
                        <button onclick="downloadPDF()" class="btn btn-primary">Download PDF</button>
                    `;
                    this.parentNode.appendChild(errorDiv);
                });
            }
        });
    </script>
</body>
</html>