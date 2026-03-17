<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <link rel="stylesheet" href="{{ asset('css/announcement.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>       
        .reports-content {
            flex: 1;
            background: #d4edda;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            border-radius: 15px;
            min-height: 100vh; /* Extend green background to full viewport height */
        }

        .reports-title {
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 1135px;
        }

        .report-button {
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            padding: 30px 25px;
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            border: 2px solid transparent;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }

        .report-button .report-icon {
            font-size: 32px;
            margin-bottom: 10px;
            color: #296218;
        }

        .report-button .report-title {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .report-button .report-subtitle {
            font-size: 12px;
            font-weight: 400;
            color: #7f8c8d;
        }

        .report-button:hover {
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            border-color: rgba(255,255,255,0.3);
            color: #2c3e50;
        }

        .report-button:active {
            transform: translateY(0);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .reports-content {
                padding: 20px;
            }

            .reports-title {
                font-size: 24px;
            }

            .reports-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .report-button {
                padding: 20px 15px;
                min-height: 100px;
            }

            .report-button .report-icon {
                font-size: 28px;
                margin-bottom: 8px;
            }

            .report-button .report-title {
                font-size: 16px;
                margin-bottom: 3px;
            }

            .report-button .report-subtitle {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <!-- Reports Content -->
            <div class="supplies-container">
                <div class="supplies-header">
                    <h1 class="supplies-title">
                        <i class="fas fa-chart-line"></i>
                        Reports
                    </h1>
                </div>

                <div class="reports-grid">
                    <a href="{{ route('client.report.rpci') }}" class="report-button">
                        <i class="fas fa-clipboard-list report-icon"></i>
                        <div class="report-title">RPCI</div>
                        <div class="report-subtitle">Report on the Physical Count of Inventory</div>
                    </a>

                    <a href="{{ route('client.report.rsmi') }}" class="report-button">
                        <i class="fas fa-boxes report-icon"></i>
                        <div class="report-title">RSMI</div>
                        <div class="report-subtitle">Report of Supplies and Materials Issued</div>
                    </a>

                    <a href="{{ route('client.report.iirup') }}" class="report-button">
                        <i class="fas fa-tools report-icon"></i>
                        <div class="report-title">IIRUP</div>
                        <div class="report-subtitle">INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY</div>
                    </a>

                    <a href="{{ route('client.report.rpc-ppe') }}" class="report-button">
                        <i class="fas fa-building report-icon"></i>
                        <div class="report-title">RPC PPE</div>
                        <div class="report-subtitle">Report on the Property Plant and Equipment</div>
                    </a>

                    <a href="{{ route('client.report.rpc-semi-high') }}" class="report-button">
                        <i class="fas fa-arrow-up report-icon"></i>
                        <div class="report-title">RPC Semi Expendable Properties (High Value)</div>
                        <div class="report-subtitle">Semi-Expandable Properties ₱50,000 and above</div>
                    </a>

                    <a href="{{ route('client.report.rpc-semi-low') }}" class="report-button">
                        <i class="fas fa-arrow-down report-icon"></i>
                        <div class="report-title">RPC Semi Expendable Properties (Low Value)</div>
                        <div class="report-subtitle">Semi-Expandable Properties below ₱50,000</div>
                    </a>
                </div>
            </div>
        </div>
        </div>   
    </div>

    <script>
        // Add any additional JavaScript functionality here
        $(document).ready(function() {
            // Add loading states or animations if needed
            $('.report-button').on('click', function() {
                $(this).css('opacity', '0.7');
            });
        });
    </script>

    @include('layouts.core.footer')
</body>
</html>
