<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepaKash - Privacy Policy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
        extend: {
            colors: {
            primary: {
                DEFAULT: '#0f753a',
                50: '#f0f9f4',
                100: '#dcf2e4',
                200: '#bbe5cd',
                300: '#8dd2ac',
                400: '#58b885',
                500: '#0f753a',
                600: '#0d6632',
                700: '#0b5529',
                800: '#094420',
                900: '#07381b',
            },
            secondary: {
                DEFAULT: '#2c8a53',
                50: '#f1f9f4',
                100: '#def2e6',
                200: '#c0e5d1',
                300: '#93d1b0',
                400: '#5fb588',
                500: '#2c8a53',
                600: '#237643',
                700: '#1d6137',
                800: '#194e2e',
                900: '#154025',
            },
            accent: {
                DEFAULT: '#efd050',
                50: '#fefce8',
                100: '#fef9c3',
                200: '#fef08a',
                300: '#fde047',
                400: '#efd050',
                500: '#eab308',
                600: '#ca8a04',
                700: '#a16207',
                800: '#854d0e',
                900: '#713f12',
                hover: '#f4dc74',
            },
            text: {
                DEFAULT: '#333333',
                light: '#666666',
            },
            background: '#e8e8e8',
            'card-bg': '#ffffff',
            success: '#0f753a',
            danger: '#e74c3c',
            warning: '#f39c12',
            }
        }
        }
    }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0f753a 0%, #2c8a53 100%);
        }
        
        .phone-shadow {
            filter: drop-shadow(0 25px 50px rgba(15, 117, 58, 0.15));
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-delayed {
            animation: float 6s ease-in-out infinite;
            animation-delay: -3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        
        .pulse-ring {
            animation: pulse-ring 2s infinite;
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(0.33);
                opacity: 1;
            }
            80%, 100% {
                transform: scale(2.33);
                opacity: 0;
            }
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
        }
        
        .card-glow {
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(15, 117, 58, 0.1);
        }
        
        .card-glow:hover {
            box-shadow: 0 10px 40px rgba(15, 117, 58, 0.2);
            transform: translateY(-2px);
        }
        
        /* Privacy Policy Specific Styles */
        .privacy-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .privacy-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .privacy-header img {
            max-width: 220px;
            margin-bottom: 20px;
        }
        
        .privacy-subtitle {
            font-size: 28px;
            font-weight: 700;
            color: #0f753a;
            margin-bottom: 10px;
        }
        
        .last-updated {
            text-align: right;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .intro-section {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        h2 {
            font-size: 24px;
            font-weight: 600;
            color: #0f753a;
            margin: 40px 0 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2c8a53;
            margin: 25px 0 15px;
        }
        
        .privacy-section {
            margin-bottom: 30px;
        }
        
        .privacy-section p {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555;
        }
        
        .privacy-section ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        .privacy-section li {
            margin-bottom: 10px;
            color: #555;
            position: relative;
            padding-left: 20px;
        }
        
        .privacy-section li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #0f753a;
            font-weight: bold;
        }
        
        .highlight-box {
            background-color: #f0f9f4;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            border-left: 4px solid #0f753a;
        }
        
        .highlight-box h3 {
            color: #0f753a;
            margin-top: 0;
        }
        
        .highlight-box p {
            margin-bottom: 0;
            color: #555;
        }
        
        .contact-section {
            background-color: #f5f5f5;
            padding: 25px;
            border-radius: 8px;
            margin-top: 40px;
        }
        
        .contact-section p {
            margin-bottom: 10px;
            color: #555;
        }
        
        .contact-section strong {
            color: #333;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-primary-50 to-secondary-50 overflow-x-hidden">
    <!-- Enhanced Navigation -->
    @include('partials.header');
    
    <!-- Privacy Policy Content -->
    <div class="privacy-container">
        <div class="privacy-header">
            <img src="" alt="Stepakash">
            <div class="privacy-subtitle">Privacy Policy</div>
        </div>
        
        <div class="last-updated">
            <strong>Last Updated:</strong> August 8, 2025
        </div>

        <div class="intro-section">
            <p>At <strong>StepaKash</strong>, your privacy is our priority. This Privacy Policy outlines how we collect, use, share, and safeguard your personal data when you engage with our digital financial services.</p>
        </div>

        <h2>Information We Collect</h2>
        <div class="privacy-section">
            <p>We collect certain personal data to provide secure, reliable, and seamless service. This includes:</p>
            <h3>Personal Information</h3>
            <ul>
                <li>Full Name</li>
                <li>Phone Number</li>
                <li>Financial Details (e.g., Deriv Account Information)</li>
                <li>Device Information and IP Address</li>
            </ul>
        </div>

        <h2>How We Use Your Information</h2>
        <div class="privacy-section">
            <p>Your information enables us to:</p>
            <ul>
                <li>Operate and maintain StepaKash services</li>
                <li>Process transactions and facilitate payments</li>
                <li>Verify your identity and prevent fraud</li>
                <li>Meet legal and regulatory obligations</li>
                <li>Communicate essential account updates</li>
                <li>Improve platform features and user experience</li>
            </ul>
        </div>

        <h2>When We Share Your Information</h2>
        <div class="privacy-section">
            <p>We may disclose your information only in the following cases:</p>
            <ul>
                <li><strong>Legal Requirements:</strong> With regulatory authorities or law enforcement as mandated by law</li>
                <li><strong>Service Providers:</strong> With partners who support service delivery (e.g., infrastructure, customer support)</li>
                <li><strong>Financial Institutions:</strong> To execute transactions securely</li>
                <li><strong>Business Restructuring:</strong> During mergers, acquisitions, or business transfers</li>
                <li><strong>With Your Consent:</strong> In cases where you explicitly authorize it</li>
            </ul>
        </div>

        <div class="highlight-box">
            <h3>Important Note</h3>
            <p><strong>Note:</strong> We do not sell, rent, or trade your personal data to third parties for marketing. Your financial data is encrypted and stored in compliance with industry-standard protocols.</p>
        </div>

        <h2>Data Security</h2>
        <div class="privacy-section">
            <p>We employ strict measures to protect your personal information, including:</p>
            <ul>
                <li>End-to-end encryption of all data and transactions</li>
                <li>Multi-factor authentication (MFA) for user access</li>
                <li>Regular security audits and threat monitoring</li>
                <li>Secure, monitored data centers</li>
                <li>Employee training on data privacy and handling</li>
            </ul>
        </div>

        <h2>Your Rights</h2>
        <div class="privacy-section">
            <p>You have the right to:</p>
            <ul>
                <li><strong>Access</strong> the personal data we hold about you</li>
                <li><strong>Request corrections</strong> to inaccurate or outdated information</li>
                <li><strong>Request deletion</strong> of your data (subject to compliance requirements)</li>
                <li><strong>Object to or restrict processing</strong> of your data</li>
                <li><strong>Request portability</strong> of your personal data</li>
                <li><strong>Withdraw consent</strong> where previously granted</li>
            </ul>
            <p>To exercise any of these rights, please contact us via the details provided below.</p>
        </div>

        <h2>Data Retention</h2>
        <div class="privacy-section">
            <p>We retain your personal data only as long as necessary to:</p>
            <ul>
                <li>Deliver our services</li>
                <li>Fulfill legal and regulatory requirements</li>
                <li>Resolve disputes and enforce agreements</li>
                <li>Prevent fraud and ensure platform security</li>
            </ul>
            <p><strong>Standard retention period:</strong> 7 years from account closure, in line with financial regulatory standards.</p>
        </div>

        <h2>Updates to This Policy</h2>
        <div class="privacy-section">
            <p>We may revise this Privacy Policy periodically. In the event of material changes, we will:</p>
            <ul>
                <li>Post the updated version on our official website</li>
                <li>Update the "Effective Date"</li>
                <li>Notify you where appropriate</li>
            </ul>
            <p>Continued use of StepaKash after changes are made implies acceptance of the revised policy.</p>
        </div>

        <div class="contact-section">
            <h2>Contact Us</h2>
            <p>For any questions, concerns, or data access requests, reach out to us via:</p>
            <p><strong>Email:</strong> info@stepakash.com</p>
            <p><strong>Phone:</strong> +254 741 554 994</p>
            <p><strong>Address:</strong> Nairobi, Kenya</p>
        </div>
    </div>

    <!-- Footer -->
    @include('partials.footer')
</body>
</html>