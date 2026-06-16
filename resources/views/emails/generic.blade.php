<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        /* Some email clients will use these styles */
        @media screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
            .content {
                padding: 20px !important;
            }
            .header {
                padding: 20px !important;
            }
            .footer {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f5f7fa; color: #333333; line-height: 1.6;">
    <!-- Main Container -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;">
        <tr>
            <td align="center" valign="top" style="padding: 40px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td align="center" valign="top" class="header" style="background-color: #4f46e5; padding: 30px; text-align: center;">
                            <!-- SVG Logo -->
                            <svg width="60" height="60" viewBox="0 0 24 24" style="fill: #ffffff;">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                            </svg>
                            <h1 style="margin: 20px 0 0; color: #ffffff; font-size: 24px; font-weight: 600;">School Management System</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td align="left" valign="top" class="content" style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px; color: #333333; font-size: 22px; font-weight: 600;">{{ $title }}</h2>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 20px; background-color: #f9fafb; border-radius: 6px; padding: 15px;">
                                <tr>
                                    <td style="padding-right: 10px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" style="fill: #6b7280;">
                                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
                                        </svg>
                                    </td>
                                    <td style="font-size: 14px; color: #6b7280;"><strong>From:</strong> {{ $fromEmail }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-right: 10px; padding-top: 10px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" style="fill: #6b7280;">
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                                        </svg>
                                    </td>
                                    <td style="font-size: 14px; color: #6b7280; padding-top: 10px;"><strong>To:</strong> {{ $toEmail }}</td>
                                </tr>
                            </table>
                            
                            <div style="font-size: 16px; color: #4b5563; margin-bottom: 30px; line-height: 1.6;">
                                {!! $description !!}
                            </div>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" valign="top" class="footer" style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <!-- Social Media Icons -->
                                                <td style="padding: 0 10px;">
                                                    <a href="#" target="_blank">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" style="fill: #6b7280;">
                                                            <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"></path>
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td style="padding: 0 10px;">
                                                    <a href="#" target="_blank">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" style="fill: #6b7280;">
                                                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td style="padding: 0 10px;">
                                                    <a href="#" target="_blank">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" style="fill: #6b7280;">
                                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="color: #6b7280; font-size: 14px; padding-bottom: 10px;">
                                        Sent via School Management System
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="color: #9ca3af; font-size: 12px;">
                                        © 2025 School Management System. All rights reserved.
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 20px; color: #9ca3af; font-size: 12px;">
                                        <a href="#" style="color: #6b7280; text-decoration: underline; margin: 0 10px;">Unsubscribe</a>
                                        <a href="#" style="color: #6b7280; text-decoration: underline; margin: 0 10px;">Privacy Policy</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>