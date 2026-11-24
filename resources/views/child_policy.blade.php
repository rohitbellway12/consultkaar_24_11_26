<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Consultkaar · Child Safety Standards Policy</title>
  <!-- Tailwind CSS v3 + Google Fonts (Inter) for clean modern typography -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Custom configuration to extend Tailwind defaults -->
  <style>
    /* subtle smooth scroll + focus outline */
    html {
      scroll-behavior: smooth;
    }
    body {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      background: #f9fafc;
    }
    /* custom card hover effect & policy section polish */
    .policy-card {
      transition: all 0.2s ease;
    }
    .policy-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.08), 0 4px 8px -4px rgba(0, 0, 0, 0.02);
    }
    /* enforce better list styles */
    .prose-list li {
      margin-bottom: 0.5rem;
    }
    .border-accent {
      border-left-width: 4px;
    }
    @media (max-width: 640px) {
      .container-padding {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
      }
    }
  </style>
  <!-- preconnect for Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
  @extends('layouts.landing.app')

@section('title','Privacy Policy')

@section('content')
  <!-- Main container with soft background & spacing -->
  <main class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-10 md:py-14">
    
    <!-- header / hero section -->
    <div class="mb-10 md:mb-12 text-center md:text-left">
      <div class="inline-flex items-center gap-2 bg-indigo-50 rounded-full px-4 py-1.5 text-indigo-700 text-sm font-medium mb-5 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10 4v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2m8 0H8m8 0h.01M8 9h.01M16 9h.01" />
        </svg>
        <span>Zero tolerance · Child protection first</span>
      </div>
      <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 leading-tight">
        Child Safety Standards <br class="hidden sm:block">Policy
      </h1>
      <div class="mt-4 h-1 w-20 bg-indigo-500 rounded-full hidden md:block"></div>
      <p class="mt-5 text-gray-600 text-lg max-w-2xl leading-relaxed">
        At Consultkaar, we are committed to maintaining a safe and respectful platform for all users. 
        We have a <strong class="text-gray-900">zero‑tolerance policy</strong> toward child sexual abuse, exploitation, 
        and any related harmful behavior or content.
      </p>
    </div>

    <!-- grid layout for policy sections: each card contains one principle -->
    <div class="grid grid-cols-1 gap-7 md:gap-8">
      
      <!-- Section 1: Zero Tolerance -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden policy-card transition-all">
        <div class="p-6 sm:p-7">
          <div class="flex items-start gap-4">
            <div class="bg-red-50 rounded-xl p-2.5 shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-900">1. Zero Tolerance for Child Sexual Abuse and Exploitation</h2>
              <p class="mt-2 text-gray-600">Consultkaar strictly prohibits any content, activity, or behavior that involves, promotes, depicts, or facilitates child sexual abuse or exploitation. This includes, but is not limited to:</p>
              <ul class="mt-4 space-y-2 text-gray-700 list-disc list-inside marker:text-red-400">
                <li><span class="font-medium">Child Sexual Abuse Material (CSAM)</span></li>
                <li><span class="font-medium">Sexual exploitation or grooming</span> of minors</li>
                <li><span class="font-medium">Solicitation</span> involving minors</li>
                <li>Any inappropriate sexualized content involving children</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 2: Prohibited Content -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden policy-card">
        <div class="p-6 sm:p-7">
          <div class="flex items-start gap-4">
            <div class="bg-amber-50 rounded-xl p-2.5 shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-900">2. Prohibited Content and Behavior</h2>
              <p class="mt-2 text-gray-600">Users may not upload, share, distribute, request, store, or engage with any content or behavior related to the sexual exploitation of minors. Any such activity is a serious violation of our platform rules and will result in immediate enforcement action.</p>
              <div class="mt-3 bg-gray-50 rounded-xl p-3 border-l-4 border-amber-400 text-sm text-gray-700">
                <span class="font-medium">⛔ Strict prohibition:</span> Any form of engagement with exploitative material, including indirect promotion or encouragement.
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 3: Reporting Mechanism -->
      <!--<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden policy-card">-->
      <!--  <div class="p-6 sm:p-7">-->
      <!--    <div class="flex items-start gap-4">-->
      <!--      <div class="bg-blue-50 rounded-xl p-2.5 shrink-0">-->
      <!--        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">-->
      <!--          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />-->
      <!--          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707" />-->
      <!--        </svg>-->
      <!--      </div>-->
      <!--      <div>-->
      <!--        <h2 class="text-xl font-bold text-gray-900">3. Reporting Mechanism</h2>-->
      <!--        <p class="mt-2 text-gray-600">Consultkaar provides users with a way to report suspicious accounts, abusive behavior, or harmful content within the app. We review such reports promptly and take appropriate action where necessary.</p>-->
      <!--        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">-->
      <!--          <span class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full text-gray-700"><span class="text-blue-500 text-lg">📱</span> In-app reporting</span>-->
      <!--          <span class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full text-gray-700"><span class="text-green-600">⚡</span> 24/7 review priority</span>-->
      <!--          <span class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full text-gray-700"><span class="text-amber-500">🛡️</span> Anonymized option</span>-->
      <!--        </div>-->
      <!--      </div>-->
      <!--    </div>-->
      <!--  </div>-->
      <!--</div>-->

      <!-- Section 4: Enforcement Actions -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden policy-card">
        <div class="p-6 sm:p-7">
          <div class="flex items-start gap-4">
            <div class="bg-purple-50 rounded-xl p-2.5 shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-900">3. Enforcement Actions</h2>
              <p class="mt-2 text-gray-600">If we become aware of content or behavior that violates these child safety standards, we may take actions including:</p>
              <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="flex items-center gap-2 text-gray-700"><span class="h-1.5 w-1.5 bg-red-500 rounded-full"></span> Immediate removal of offending content</div>
                <div class="flex items-center gap-2 text-gray-700"><span class="h-1.5 w-1.5 bg-red-500 rounded-full"></span> Suspension or permanent account termination</div>
                <div class="flex items-center gap-2 text-gray-700"><span class="h-1.5 w-1.5 bg-red-500 rounded-full"></span> Restriction of access to platform features</div>
                <div class="flex items-center gap-2 text-gray-700"><span class="h-1.5 w-1.5 bg-red-500 rounded-full"></span> Preservation of information for legal investigation</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 5: Cooperation with Law Enforcement -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden policy-card">
        <div class="p-6 sm:p-7">
          <div class="flex items-start gap-4">
            <div class="bg-gray-100 rounded-xl p-2.5 shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M9 3v3m6-3v3M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11h8M8 15h4" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-900">4. Cooperation with Law Enforcement</h2>
              <p class="mt-2 text-gray-600">Consultkaar complies with applicable child safety laws and regulations and cooperates with relevant law enforcement and regulatory authorities in connection with reports or investigations involving child sexual abuse or exploitation.</p>
              <div class="mt-3 flex items-center gap-2 text-sm bg-slate-50 rounded-lg p-3">
                <span class="font-semibold text-slate-800">📌 Legal compliance:</span>
                <span class="text-slate-600">We preserve records & support official inquiries to protect minors worldwide.</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 6: Safety Commitment -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden policy-card">
        <div class="p-6 sm:p-7">
          <div class="flex items-start gap-4">
            <div class="bg-emerald-50 rounded-xl p-2.5 shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-900">5. Safety Commitment</h2>
              <p class="mt-2 text-gray-600">We are committed to continuously improving our moderation, reporting, and safety practices to help protect users and prevent abuse on our platform.</p>
              <div class="mt-3 flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-sm px-3 py-1 rounded-full">🔍 AI-assisted moderation</span>
                <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-sm px-3 py-1 rounded-full">🧑‍⚖️ Human review escalation</span>
                <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-sm px-3 py-1 rounded-full">📈 Regular policy updates</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 7: Contact Information (highlighted card) -->
      <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-sm border border-indigo-100 overflow-hidden policy-card">
        <div class="p-6 sm:p-7">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-3">
              <div class="bg-white rounded-full p-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900">6. Contact Information</h2>
                <p class="text-gray-700 mt-1">For child safety concerns or questions regarding this policy, reach out to our dedicated safety team.</p>
              </div>
            </div>
            <div class="bg-white rounded-xl px-5 py-3 shadow-sm border border-indigo-200 flex items-center gap-2">
              <span class="text-indigo-600 font-mono text-base font-semibold">consultkaar@gmail.com</span>
              <button onclick="navigator.clipboard?.writeText('consultkaar@gmail.com'); alert('Email address copied to clipboard')" class="text-gray-400 hover:text-indigo-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </button>
            </div>
          </div>
          <div class="mt-5 text-sm text-gray-500 border-t border-indigo-100 pt-4 flex flex-wrap justify-between items-center">
            <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span> Reports handled with confidentiality</span>
            <span class="text-indigo-600">✉️ consultkaar@gmail.com — 24h response for safety escalations</span>
          </div>
        </div>
      </div>
    </div>

    <!-- additional commitment footer / trust badge -->
    <div class="mt-12 text-center border-t border-gray-200 pt-8 pb-4">
      <div class="flex flex-wrap justify-center gap-x-6 gap-y-3 text-sm text-gray-500">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span>Zero tolerance enforced</span>
        </div>
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10 4v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2m8 0H8m8 0h.01M8 9h.01M16 9h.01"></path></svg>
          <span>Proactive monitoring</span>
        </div>
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 01-6.001 0M18 7l-3 9m-3-9l-6-2m6-2l6 2m-6-2v12"></path></svg>
          <span>Industry safety standards</span>
        </div>
      </div>
      <p class="text-xs text-gray-400 mt-6">© Consultkaar — Child Safety Standards Policy v1.0 · All rights reserved</p>
    </div>
  </main>
@endsection

 
</body>
</html>