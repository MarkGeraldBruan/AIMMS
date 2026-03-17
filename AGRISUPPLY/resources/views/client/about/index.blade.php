<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .about-container {
            background-color: #296218;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
            max-width: 100%;
            box-sizing: border-box;
        }

        .about-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .about-title {
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 1135px;
        }

        .about-button {
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

        .about-button .about-icon {
            font-size: 32px;
            margin-bottom: 10px;
            color: #296218;
        }

        .about-button .about-button-title {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .about-button .about-subtitle {
            font-size: 12px;
            font-weight: 400;
            color: #7f8c8d;
        }

        .about-button:hover {
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            border-color: rgba(255,255,255,0.3);
            color: #2c3e50;
        }

        .about-button:active {
            transform: translateY(0);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .about-content {
                padding: 20px;
            }

            .about-title {
                font-size: 24px;
            }

            .about-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .about-button {
                padding: 20px 15px;
                min-height: 100px;
            }

            .about-button .about-icon {
                font-size: 28px;
                margin-bottom: 8px;
            }

            .about-button .about-button-title {
                font-size: 16px;
                margin-bottom: 3px;
            }

            .about-button .about-subtitle {
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

            <!-- About Content -->
            <div class="supplies-container">
                <!-- Header Section -->
                <div class="supplies-header-section">
                    <h1 class="supplies-title">
                        <i class="fas fa-info-circle"></i>
                        About
                    </h1>
                </div>

                <div class="supplies-content">
                    <div class="about-grid">
                        <button onclick="openAboutSystemModal()" class="about-button">
                            <i class="fas fa-cogs about-icon"></i>
                            <div class="about-button-title">About Our System</div>
                            <div class="about-subtitle">Learn about the ATI-RTC 1 Monitoring Management system</div>
                        </button>

                        <button onclick="openAboutUsModal()" class="about-button">
                            <i class="fas fa-users about-icon"></i>
                            <div class="about-button-title">About Us</div>
                            <div class="about-subtitle">Meet The4fumlas</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Our System Modal -->
    <div id="about-system-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>About Our System</h2>
                <span class="modal-close" onclick="closeAboutSystemModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="about-box">
                    <img src="{{ asset('assets/img/AboutOurSystem.png') }}" alt="About Our System" style="width: 100%; max-width: 400px; height: auto; margin-bottom: 20px; border-radius: 10px;">
                    <p>The Monitoring Management System for ATI-RTC 1 is a digital platform developed to enhance the organization’s monitoring and management processes. It automates data entry and tracking to help employees manage information efficiently and reduce workload. The system also includes an automatic report generation feature, allowing users to create and print reports easily. It ensures accurate data recording and promotes a more organized and transparent workflow within ATI-RTC 1.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- About Us Modal -->
    <div id="about-us-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>About Us</h2>
                <span class="modal-close" onclick="closeAboutUsModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="about-box">
                    <img src="{{ asset('assets/img/AboutUs.png') }}" alt="About Us" style="width: 100%; max-width: 400px; height: auto; margin-bottom: 20px; border-radius: 10px;">
                    <p>We are The 4mulas, a team of passionate Bachelor of Science in Information Technology students from Universidad de Dagupan committed to developing innovative and efficient digital solutions that address real-world challenges. Our capstone project, the Monitoring Management System for ATI-RTC 1 reflects our dedication to improving productivity and simplifying management tasks through technology. 
Through this project, we aim to support ATI-RTC 1 in achieving a more systematic and reliable digital workflow.</p>
                    <br>
                    <p>Team Members: </p>
                    <p>• Christian Mathew Catubig (Project Manager)</p>
                    <p>• Diane Cruz (UI/UX Designer)</p>
                    <p>• Ralph Bolinas (Front-end Developer)</p>
                    <p>• Mark Gerald Bruan (Back-end Developer)</p>
                    <br>
                    <p>As The 4mulas, we believe technology can transform daily operations into faster, easier, and more effective processes. Our goal is to contribute to the continuous improvement of ATI-RTC 1 through innovation, collaboration, and smart digital solutions.</p>

                </div>
            </div>
        </div>
    </div>

    <script>
        // About Modal Functions
        function openAboutSystemModal() {
            document.getElementById('about-system-modal').style.display = 'flex';
        }

        function closeAboutSystemModal() {
            document.getElementById('about-system-modal').style.display = 'none';
        }

        function openAboutUsModal() {
            document.getElementById('about-us-modal').style.display = 'flex';
        }

        function closeAboutUsModal() {
            document.getElementById('about-us-modal').style.display = 'none';
        }

        // Close modals when clicking outside
        document.getElementById('about-system-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeAboutSystemModal();
            }
        });

        document.getElementById('about-us-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeAboutUsModal();
            }
        });

        // Add any additional JavaScript functionality here
        $(document).ready(function() {
            // Add loading states or animations if needed
            $('.about-button').on('click', function() {
                $(this).css('opacity', '0.7');
                setTimeout(() => {
                    $(this).css('opacity', '1');
                }, 200);
            });
        });
    </script>

    @include('layouts.core.footer')
</body>
</html>
