@push('styles')
    <style>
        .lesson-viewer {
            min-height: calc(100vh - 200px);
        }

        .lesson-sidebar {
            border-right: 1px solid #dee2e6;
        }

        .lesson-sidebar .card {
            border-radius: 0;
        }

        .lessons-list {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .lessons-list::-webkit-scrollbar {
            width: 6px;
        }

        .lessons-list::-webkit-scrollbar-track {
            background: #f7fafc;
        }

        .lessons-list::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        .lessons-list::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .lesson-item {
            transition: background-color 0.2s;
        }

        .lesson-item:hover {
            background-color: #f8f9fa !important;
        }

        .lesson-item.active {
            background-color: #e7f3ff !important;
            border-left: 3px solid #0d6efd;
        }

        .video-container {
            position: relative;
            width: 100%;
            background: #000;
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
        }

        .lesson-content .card {
            border-radius: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .lesson-sidebar {
                border-right: none;
                border-bottom: 1px solid #dee2e6;
                margin-bottom: 1rem;
            }

            .lessons-list {
                max-height: 300px !important;
            }

            .lesson-viewer .row {
                flex-direction: column;
            }
        }

        @media (max-width: 575.98px) {
            .lesson-content .p-4 {
                padding: 1rem !important;
            }

            .lesson-content h2 {
                font-size: 1.25rem;
            }
        }
    </style>
@endpush

