<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepaKash - Terms and Conditions</title>
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

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(2deg);
            }
        }

        .pulse-ring {
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.33);
                opacity: 1;
            }

            80%,
            100% {
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

        /* Terms Page Specific Styles */
        .terms-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .terms-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .terms-header img {
            max-width: 220px;
            margin-bottom: 20px;
        }

        .terms-subtitle {
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

        .intro-text {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
            font-size: 16px;
            line-height: 1.6;
        }

        .section {
            margin-bottom: 40px;
            position: relative;
            padding-left: 40px;
        }

        .section-number {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 32px;
            font-weight: 700;
            color: #0f753a;
            opacity: 0.3;
        }

        .section h3 {
            font-size: 22px;
            font-weight: 600;
            color: #0f753a;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e0e0e0;
        }

        .subsection {
            margin-bottom: 25px;
        }

        .subsection-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .section p,
        .subsection p {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555;
        }

        .section ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        .section li {
            margin-bottom: 8px;
            color: #555;
        }

        .acknowledgment {
            background-color: #f0f9f4;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
            border-left: 4px solid #0f753a;
        }

        .acknowledgment h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .acknowledgment p {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }

        .acknowledgment p:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #0f753a;
            font-weight: bold;
        }

        .checkbox-container {
            background-color: #f5f5f5;
            padding: 25px;
            border-radius: 8px;
            margin: 40px 0;
            text-align: center;
        }

        .disclaimer {
            background-color: #fff8e6;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #efd050;
            margin-top: 40px;
        }

        .disclaimer h2 {
            font-size: 20px;
            font-weight: 700;
            color: #eab308;
            margin-bottom: 15px;
        }

        .disclaimer p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-primary-50 to-secondary-50 overflow-x-hidden">
    <!-- Enhanced Navigation -->
    @include('partials.header');
    <!-- Terms Content -->
    <div class="terms-container">
        <div class="terms-header">
            <img src="" alt="Stepakash">
            <div class="terms-subtitle">Terms of Use & Conditions</div>
        </div>

        <div class="last-updated">
            <strong>Last Updated:</strong> August 1, 2025
        </div>

        <div class="intro-text">
            These Terms and Conditions ("Terms") govern your use of the services provided by StepaKash, a digital financial technology (fintech) platform ("StepaKash", "we", "us", or "our"). By creating an account or using any services on our platform, you agree to be bound by these Terms.
        </div>

        <div class="acknowledgment">
            <h2>By accepting these Terms of Use and using the StepaKash Service you acknowledge that:</h2>
            <p>(i) we are not a bank and your Account is not a bank account;</p>
            <p>(ii) Accounts are not insured by any government agency or Deriv Broker;</p>
            <p>(iii) we do not act as a trustee, fiduciary or escrow holder in respect of balances in your Account; and</p>
            <p>(iv) we do not pay you interest on any balances in your Account.</p>
        </div>

        <div class="section">
            <span class="section-number">1</span>
            <h3>Legal Relationship & Disclaimer</h3>
            <p>By accepting these Terms and using the StepaKash service, you expressly acknowledge and agree that:</p>

            <div class="subsection">
                <div class="subsection-title">1.1 Non-Banking Status</div>
                <p>StepaKash is a financial technology service provider and not a bank. Your StepaKash Wallet ("Account") is not a bank account, and you are not entitled to any bank-related privileges under your use of our service.</p>
            </div>

            <div class="subsection">
                <div class="subsection-title">1.2 No Government or Broker Insurance</div>
                <p>Your Account is not insured by any government agency or Deriv Broker, and you accept all risks associated with holding balances within our platform.</p>
            </div>

            <div class="subsection">
                <div class="subsection-title">1.3 No Trustee Relationship</div>
                <p>StepaKash does not act as a trustee, fiduciary, or escrow holder for the balances held in your Account. We are merely facilitating digital financial transactions at your direction.</p>
            </div>

            <div class="subsection">
                <div class="subsection-title">1.4 No Interest Earnings</div>
                <p>We do not pay interest on any balances maintained in your Account.</p>
            </div>
        </div>

        <div class="section">
            <span class="section-number">2</span>
            <h3>Eligibility for Use</h3>

            <div class="subsection">
                <div class="subsection-title">2.1 Minimum Age</div>
                <p>To use our services, you must be 18 years or older and possess full legal capacity to enter into a binding agreement.</p>
            </div>

            <div class="subsection">
                <div class="subsection-title">2.2 Residency</div>
                <p>You must be a legal resident of a country where StepaKash services are officially offered.</p>
            </div>
        </div>

        <div class="section">
            <span class="section-number">3</span>
            <h3>Account Use and Limitations</h3>

            <div class="subsection">
                <div class="subsection-title">3.1 Personal Use Only</div>
                <p>You may not permit any other person to access or use your StepaKash Account. Each account is personal, and you are solely responsible for maintaining its confidentiality and security.</p>
            </div>

            <div class="subsection">
                <div class="subsection-title">3.2 One Account Policy</div>
                <p>You may only open one Account with us. If you create or are suspected to have created multiple accounts (including multiple CR accounts), we reserve the right—without prior notice—to close or suspend any or all related accounts.</p>
            </div>
        </div>

        <div class="section">
            <span class="section-number">4</span>
            <h3>Transactions & Restrictions</h3>

            <div class="subsection">
                <div class="subsection-title">4.1 Transaction Approval Rights</div>
                <p>We reserve the sole right to decline or block any transaction that:</p>
                <ul>
                    <li>Appears fraudulent,</li>
                    <li>Violates these Terms or applicable laws,</li>
                    <li>Involves insufficient funds, or</li>
                    <li>Raises any compliance or risk concerns.</li>
                </ul>
            </div>

            <div class="subsection">
                <div class="subsection-title">4.2 Partner Refusal Liability</div>
                <p>We shall not be liable if DERIV, MPESA, or any third-party platform:</p>
                <ul>
                    <li>Refuses your deposit or withdrawal,</li>
                    <li>Fails to authorize a transaction, or</li>
                    <li>Experiences downtime or transactional delays.</li>
                </ul>
            </div>

            <div class="subsection">
                <div class="subsection-title">4.3 Sufficient Balance Requirement</div>
                <p>When requesting a withdrawal or payment:</p>
                <ul>
                    <li>You may not exceed the available balance in your StepaKash Wallet (including applicable fees).</li>
                    <li>Transactions that exceed your available balance will be denied.</li>
                    <li>You must also meet any minimum withdrawal threshold set on the platform. If your balance is insufficient, your request will be automatically rejected.</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <span class="section-number">5</span>
            <h3>Fees and Charges</h3>

            <div class="subsection">
                <div class="subsection-title">5.1 Service Fees</div>
                <p>All applicable transaction fees, withdrawal charges, or service costs will be deducted directly from your account. A full breakdown of fees may be published and updated periodically on our website or app.</p>
            </div>
        </div>

        <div class="section">
            <span class="section-number">6</span>
            <h3>Suspension & Termination</h3>

            <div class="subsection">
                <div class="subsection-title">6.1 Account Suspension or Termination</div>
                <p>We may suspend or permanently terminate your access to StepaKash services, without prior notice, if we reasonably believe you:</p>
                <ul>
                    <li>Violated any of these Terms,</li>
                    <li>Attempted to manipulate or exploit the platform,</li>
                    <li>Engaged in any form of fraudulent, illegal, or abusive conduct.</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <span class="section-number">7</span>
            <h3>Updates to Terms</h3>

            <div class="subsection">
                <div class="subsection-title">7.1 Right to Amend</div>
                <p>We reserve the right to modify these Terms at any time. Continued use of the StepaKash platform after changes are made will constitute your acceptance of the updated Terms.</p>
            </div>
        </div>

        <div class="section">
            <span class="section-number">8</span>
            <h3>Contact and Support</h3>
            <p>If you have any questions, disputes, or require support, you may reach out to our customer service team through our official channels listed on the website or app.</p>
        </div>

        <div class="checkbox-container">
            <h3 style="margin-bottom: 15px; border: none; text-transform: none; letter-spacing: normal;">Agreement Confirmation</h3>
            <p><strong>By creating an account or continuing to use StepaKash, you confirm that you have read, understood, and agree to abide by these Terms and Conditions.</strong></p>
        </div>

        <div class="disclaimer">
            <h2>DISCLAIMER</h2>
            <p>While your StepaKash Wallet offers fast, secure, and convenient access to digital funds, please note that it is not a bank account. In the rare event of company insolvency, the balance in your Wallet may not be covered by deposit protection schemes, and recovery of funds is not guaranteed.</p>
        </div>
    </div>

    <!-- Footer -->
    @include('partials.footer')
</body>

</html>