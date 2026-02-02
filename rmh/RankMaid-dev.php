<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RMH | Rank Maid Marketing Hub - Master V27</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <style>
      body {
        background-color: #f0f2f5;
        color: #1e293b;
        font-family: "Inter", system-ui, sans-serif;
      }

      /* Navigation */
      .nav-item {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        border-left: 3px solid transparent;
      }

      .nav-item:hover {
        background-color: #1f2937;
        color: white;
      }

      .nav-item.active {
        background-color: #ea580c;
        color: white;
        border-left: 3px solid #fb923c;
      }

      /* UI Components */
      .glass-panel {
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
      }

      .status-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
      }

      .status-active {
        background-color: #10b981;
      }

      .status-paused {
        background-color: #94a3b8;
      }

      .status-learning {
        background-color: #f59e0b;
      }

      /* Knowledge Base Specifics */
      .kb-card {
        transition: all 0.3s ease;
        border-top: 4px solid transparent;
        cursor: pointer;
      }

      .kb-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
      }

      .kb-card.expanded {
        border-color: #ea580c;
      }

      /* Typography */
      .prose h4 {
        font-weight: 800;
        color: #1e293b;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
      }

      .prose p {
        font-size: 0.875rem;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 0.75rem;
      }

      .prose ul {
        list-style: disc;
        padding-left: 1.25rem;
        font-size: 0.875rem;
        color: #475569;
        margin-bottom: 1rem;
      }

      .prose li {
        margin-bottom: 0.25rem;
      }

      .tag {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 99px;
        letter-spacing: 0.05em;
      }

      /* Tabs */
      .tab-btn {
        padding: 12px 24px;
        font-weight: 600;
        border-bottom: 2px solid transparent;
        color: #64748b;
        transition: all 0.3s;
        font-size: 0.9rem;
      }

      .tab-btn.active {
        color: #ea580c;
        border-bottom-color: #ea580c;
        background-color: #fff7ed;
        border-radius: 8px 8px 0 0;
      }

      .tab-btn:hover {
        color: #ea580c;
      }

      /* Tables */
      .facebook-table td,
      .facebook-table th {
        border: 1px solid #000000;
        padding: 6px;
        vertical-align: top;
      }

      .facebook-table th {
        font-size: 11px;
      }
      .facebook-table thead tr {
        background-color: #333 !important;
        color: white;
        font-weight: 600;
        text-align: left;
        padding: 12px 15px;
        font-size: 0.9rem;
        text-transform: uppercase;
      }

      .facebook-table tbody tr:nth-of-type(even) {
        background-color: #f8f8f8;
      }

      /* Modal & Scrollbar */
      .modal-overlay {
        background-color: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(8px);
      }

      .modal-content {
        animation: slideUp 0.3s ease-out;
      }

      @keyframes slideUp {
        from {
          opacity: 0;
          transform: translateY(20px);
        }

        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .fade-in {
        animation: fadeIn 0.4s ease-out;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(8px);
        }

        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      ::-webkit-scrollbar {
        width: 6px;
      }

      ::-webkit-scrollbar-track {
        background: transparent;
      }

      ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
      }
    </style>
  </head>

  <body class="h-screen flex overflow-hidden text-sm">
    <aside
      class="w-64 bg-slate-900 text-slate-400 flex flex-col shadow-2xl z-20 flex-shrink-0"
    >
      <div
        class="h-16 flex items-center px-6 bg-slate-950 border-b border-slate-800"
      >
        <i class="fa-solid fa-layer-group text-orange-600 text-xl mr-3"></i>
        <div>
          <h1 class="font-black text-white text-xl tracking-tight">RMH</h1>
          <p
            class="text-[9px] uppercase font-bold text-slate-500 tracking-wider"
          >
            Master V27.0
          </p>
        </div>
      </div>

      <nav class="flex-1 overflow-y-auto py-4 space-y-1">
        <div
          class="px-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest"
        >
          Core Operations
        </div>
        <div class="px-3 mb-2">
          <div
            onclick="App.router('global')"
            id="nav-global"
            class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-r-md active"
          >
            <i class="fa-solid fa-earth-americas w-5 text-center mr-3"></i>
            Global Command
          </div>
          <div
            onclick="App.router('goals')"
            id="nav-goals"
            class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-r-md"
          >
            <i class="fa-solid fa-bullseye w-5 text-center mr-3"></i> Goals &
            Alignment
          </div>
          <div
            onclick="App.router('knowledge')"
            id="nav-knowledge"
            class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-r-md"
          >
            <i class="fa-solid fa-book-open w-5 text-center mr-3"></i>
            Information Center
          </div>
        </div>

        <div
          class="px-6 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest"
        >
          Paid Channels
        </div>
        <div class="px-3 space-y-1">
          <div
            onclick="App.router('facebook')"
            id="nav-facebook"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-brands fa-facebook w-5 text-center mr-3"></i> Facebook
            Ads
          </div>
          <div
            onclick="App.router('google')"
            id="nav-google"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-brands fa-google w-5 text-center mr-3"></i> Google Ads
          </div>
          <div
            onclick="App.router('reddit')"
            id="nav-reddit"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-brands fa-reddit w-5 text-center mr-3"></i> Reddit Ads
          </div>
          <div
            onclick="App.router('quora')"
            id="nav-quora"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-brands fa-quora w-5 text-center mr-3"></i> Quora Ads
          </div>
          <div
            onclick="App.router('taboola')"
            id="nav-taboola"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-solid fa-bullhorn w-5 text-center mr-3"></i> Taboola
          </div>
          <div
            onclick="App.router('adroll')"
            id="nav-adroll"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-solid fa-rectangle-ad w-5 text-center mr-3"></i> AdRoll
          </div>
        </div>

        <div
          class="px-6 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest"
        >
          Outreach
        </div>
        <div class="px-3 space-y-1">
          <div
            onclick="App.router('linkedin')"
            id="nav-linkedin"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-brands fa-linkedin w-5 text-center mr-3"></i> LinkedIn
          </div>
          <div
            onclick="App.router('email')"
            id="nav-email"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-solid fa-envelope w-5 text-center mr-3"></i> Email
            Database
          </div>
          <div
            onclick="App.router('propstream')"
            id="nav-propstream"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-solid fa-house-signal w-5 text-center mr-3"></i>
            PropStream
          </div>
          <div
            onclick="App.router('craigslist')"
            id="nav-craigslist"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-solid fa-list w-5 text-center mr-3"></i> Craigslist
          </div>
          <div
            onclick="App.router('biggerpockets')"
            id="nav-biggerpockets"
            class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"
          >
            <i class="fa-brands fa-get-pocket w-5 text-center mr-3"></i>
            BiggerPockets
          </div>
        </div>
      </nav>

      <div
        class="p-4 bg-slate-950 border-t border-slate-800 flex items-center gap-3"
      >
        <div
          class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white text-xs font-bold"
        >
          JD
        </div>
        <div>
          <p class="text-xs text-white font-bold">Ops Lead</p>
          <p class="text-[10px] text-green-400">● Live Mode</p>
        </div>
      </div>
    </aside>

    <main class="flex-1 flex flex-col relative h-full bg-[#F3F4F6]">
      <header
        class="h-16 bg-white border-b border-gray-200 flex justify-between items-center px-8 z-10 shadow-sm"
      >
        <div>
          <h2 id="page-title" class="text-xl font-bold text-gray-800">
            Global Command
          </h2>
          <p id="page-subtitle" class="text-xs text-gray-500 font-medium">
            Enterprise View
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right hidden md:block">
            <p
              class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"
            >
              Live Sync
            </p>
            <p class="text-xs font-mono text-emerald-600 font-bold">
              UPDATED: JUST NOW
            </p>
          </div>

          <button
            onclick="App.triggerFacebookUpload()"
            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow-lg shadow-orange-100 transition-all text-xs font-bold flex items-center gap-2"
          >
            <i class="fa-solid fa-rotate"></i> Upload Data
          </button>
        </div>
      </header>

      <div id="content-area" class="flex-1 overflow-y-auto p-8 fade-in"></div>
    </main>

    <div
      id="taskModal"
      class="fixed inset-0 modal-overlay z-50 hidden flex items-center justify-center p-4"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl modal-content overflow-hidden border border-gray-200"
      >
        <div
          class="bg-slate-900 px-8 py-6 flex justify-between items-center border-b border-slate-700"
        >
          <div>
            <h3 class="text-xl font-black text-white flex items-center gap-2">
              <i class="fa-solid fa-brain text-orange-500"></i> Strategic
              Analysis
            </h3>
            <p class="text-slate-400 text-xs mt-1">
              RMH Intelligence Engine v2.5
            </p>
          </div>
          <button
            onclick="
              document.getElementById('taskModal').classList.add('hidden')
            "
            class="text-gray-400 hover:text-white bg-slate-800 hover:bg-slate-700 w-8 h-8 rounded-full flex items-center justify-center transition-all"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="p-0" id="modal-body"></div>
      </div>
    </div>

    <script>
      // ===============================
      // CSV ROW PARSER (REQUIRED)
      // Handles quoted commas correctly
      // ===============================
      const parseCSVRow = (row) => {
        const result = [];
        let current = "";
        let inQuotes = false;

        for (let i = 0; i < row.length; i++) {
          const char = row[i];

          if (char === '"' && row[i + 1] === '"') {
            current += '"';
            i++;
          } else if (char === '"') {
            inQuotes = !inQuotes;
          } else if (char === "," && !inQuotes) {
            result.push(current.trim());
            current = "";
          } else {
            current += char;
          }
        }

        result.push(current.trim());
        return result;
      };

      // Use relative paths for API calls, just like index.php.
      // This removes the leading slash from paths to make them relative.
      const api = (path) => (path.startsWith("/") ? path.substring(1) : path);

      Chart.register(ChartDataLabels);

      // 1. DATA STORE
      const DB = {
        facebook: {
          id: "facebook",
          name: "Facebook Ads",
          type: "paid",
          color: "#1877F2",
          goals: { targetCPA: 175, targetROAS: 4.0 },

          // ✅ Current vs Previous storage
          currentRows: [],
          previousRows: [],

          // Used for table display
          csvRows: [],

          // Full archive
          _allRows: [],

          metrics: {
            spend: { current: 0, prev: 0 },
            leads: { current: 0, prev: 0 },
            bookings: { current: 0, prev: 0 },
            costPerResult: { current: 0 },
            revenue: { current: 0, prev: 0 },
          },

          campaigns: [
            {
              id: "C1",
              name: "Prospecting_Broad_V3",
              status: "active",
              spend: 2800,
              clicks: 2100,
              leads: 85,
              bookings: 12,
              revenue: 18000,
            },
            {
              id: "C2",
              name: "Retargeting_Web",
              status: "active",
              spend: 900,
              clicks: 450,
              leads: 30,
              bookings: 6,
              revenue: 7000,
            },
          ],

          optimizations: [
            {
              id: 101,
              title: 'Scale "Prospecting_Broad" Budget',
              type: "Scale Opportunity",
              confidence: 94,
              impact: "High",
              rootCause: "Campaign has sustained ROAS > 6.0x for 14 days.",
              projection: {
                leads: "+12 Leads/mo",
                revenue: "+$4,500 Revenue",
                cpa: "Maintain <$45",
              },
              instruction:
                "1. Navigate to <strong>Ads Manager > Campaigns</strong>.\n2. Select <strong>Prospecting_Broad_V3</strong>.\n3. Increase Daily Budget from <strong>$100 to $120</strong> (+20%).",
            },
            {
              id: 102,
              title: "Retargeting Creative Fatigue",
              type: "Creative Alert",
              confidence: 88,
              impact: "Medium",
              rootCause: "Frequency > 4.5 and CTR dropped to 0.6%.",
              projection: {
                leads: "Stabilize",
                revenue: "Prevent Churn",
                cpa: "Lower by 10%",
              },
              instruction:
                '1. Go to <strong>Retargeting Ad Set</strong>.\n2. Turn off "Image_Ad_04".\n3. Activate "Video_Testimonial_02".',
            },
          ],

          history: { roas: [2.1, 2.4, 3.8, 3.2, 4.0, 5.8, 6.2] },
        },

        google: {
          id: "google",
          name: "Google Ads",
          type: "paid",
          color: "#EA4335",
          goals: { targetCPA: 175, targetROAS: 3.5 },
          metrics: {
            spend: { current: 3100, prev: 3000 },
            leads: { current: 45, prev: 50 },
            bookings: { current: 8, prev: 10 },
            revenue: { current: 12000, prev: 15000 },
          },
          campaigns: [
            {
              id: "G1",
              name: "Brand_Search",
              status: "active",
              spend: 2100,
              clicks: 1200,
              leads: 35,
              bookings: 6,
              revenue: 10000,
            },
          ],
          optimizations: [
            {
              id: 201,
              title: "Negative Keyword Addition",
              type: "Budget Protection",
              confidence: 98,
              impact: "High",
              rootCause: "Detected $240 spend on low-intent search terms.",
              projection: {
                leads: "N/A",
                revenue: "Save $800/mo",
                cpa: "Reduce by 15%",
              },
              instruction:
                '1. Go to <strong>Keywords > Search Terms</strong>.\n2. Exclude "rent", "job".',
            },
          ],
          history: { roas: [4.1, 4.2, 3.8, 3.5, 3.2, 3.0, 3.1] },
        },

        reddit: {
          id: "reddit",
          name: "Reddit Ads",
          type: "paid",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        quora: {
          id: "quora",
          name: "Quora Ads",
          type: "paid",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        taboola: {
          id: "taboola",
          name: "Taboola",
          type: "paid",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        adroll: {
          id: "adroll",
          name: "AdRoll",
          type: "paid",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        linkedin: {
          id: "linkedin",
          name: "LinkedIn",
          type: "organic",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        email: {
          id: "email",
          name: "Email DB",
          type: "organic",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        propstream: {
          id: "propstream",
          name: "PropStream",
          type: "organic",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        craigslist: {
          id: "craigslist",
          name: "Craigslist",
          type: "organic",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },
        biggerpockets: {
          id: "biggerpockets",
          name: "BiggerPockets",
          type: "organic",
          metrics: {
            spend: { current: 0 },
            leads: { current: 0 },
            bookings: { current: 0 },
            revenue: { current: 0 },
          },
          campaigns: [],
          optimizations: [],
          history: { roas: [] },
        },

        goals: {
          vision:
            "To be the dominant SEO & Lead Generation partner for Real Estate Investors, filling sales calendars with high-intent appointments.",
          targets: {
            appointments: { target: 45, current: 41 },
            caq: { target: 175, current: 168 },
          },
          scorecard: [
            {
              role: "VA / Operator",
              metric: "Data Accuracy",
              target: "100% Match",
              consequence: "Daily Audit",
            },
            {
              role: "Sales Rep",
              metric: "Appts Attended",
              target: "45 / Week",
              consequence: "Retraining",
            },
            {
              role: "Media Buyer",
              metric: "CAQ (Cost/Appt)",
              target: "< $175",
              consequence: "Budget Freeze",
            },
          ],
        },

        library: {
          algorithms: [
            {
              title: "Facebook Algorithm for REI",
              icon: "fa-brands fa-facebook",
              color: "border-blue-500",
              summary: "Meta's AI optimizes for engagement.",
              details: `
                                                                                                  <h4>How the Algorithm Thinks</h4>
                                                                                                  <p>Meta's algorithm doesn't care about Real Estate. It cares about <strong>user intent signals</strong>.</p>
                                                                                                  <h4>The Learning Phase</h4>
                                                                                                  <p>Every ad set requires <strong>50 conversions per week</strong> to exit Learning.</p>
                                                                                                  <h4>Strategy: Broad Targeting</h4>
                                                                                                  <p>Use broad targeting and let creative do the filtering.</p>
                                                                                                `,
            },
            {
              title: "Google Ads Quality Score",
              icon: "fa-brands fa-google",
              color: "border-red-500",
              summary: "Relevance = Lower Cost.",
              details: `
                                                                                                  <h4>The Formula</h4>
                                                                                                  <p><strong>Ad Rank = Bid × Quality Score</strong>.</p>
                                                                                                `,
            },
            {
              title: "SEO: Local Pack & E-E-A-T",
              icon: "fa-solid fa-map-location-dot",
              color: "border-emerald-500",
              summary: "Map Pack drives calls.",
              details: `
                                                                                                  <h4>Ranking Factors</h4>
                                                                                                  <p>Proximity, Relevance, Prominence.</p>
                                                                                                `,
            },
            {
              title: "Native Ads (Taboola)",
              icon: "fa-solid fa-bullhorn",
              color: "border-indigo-500",
              summary: "High volume, low intent.",
              details: `
                                                                                                  <h4>The Mindset</h4>
                                                                                                  <p>Interrupt the pattern, educate first.</p>
                                                                                                `,
            },
          ],
          sops: [
            {
              title: "Scaling Budget",
              category: "Optimization",
              steps: [
                "Check ROAS > 3.0x over last 7 days.",
                "Verify Campaign is NOT in Learning.",
                "Increase budget by exactly 20%.",
                "Log change in Daily Report.",
              ],
              details:
                "<p><strong>Why 20%?</strong> More than 20% risks resetting learning.</p>",
            },
          ],
          scripts: [
            {
              title: "SMS: New Web Lead",
              context: "Immediate text after a form fill.",
              content:
                "Hi [Name], this is [Your Name] with [Company]. I saw you were looking for a cash offer.",
              objections: "<strong>No reply?</strong><br>Send a follow up.",
            },
          ],
        },
      };

      // 2. LOGIC
      const Logic = {
        // ✅ Campaign Aggregator
        aggregateCampaignMetrics: (headers, rows) => {
          const normalize = (s) =>
            (s || "").toLowerCase().replace(/\s+/g, " ").trim();

          // ✅ Find Meta export columns
          const campaignIdx = headers.findIndex((h) =>
            normalize(h).includes("campaign name"),
          );

          const adsetIdx = headers.findIndex((h) =>
            normalize(h).includes("ad set name"),
          );

          const spendIdx = headers.findIndex((h) =>
            normalize(h).includes("amount spent"),
          );

          const clickIdx = headers.findIndex((h) =>
            normalize(h).includes("link clicks"),
          );

          const reachIdx = headers.findIndex((h) => normalize(h) === "reach");

          const imprIdx = headers.findIndex(
            (h) => normalize(h) === "impressions",
          );

          if (campaignIdx === -1) return {};

          const totals = {};

          rows.forEach((r) => {
            const campaign = r[campaignIdx] || "Unknown Campaign";
            const adset =
              adsetIdx !== -1 ? r[adsetIdx] || "Unknown Ad Set" : "All Ad Sets";

            // ✅ Key becomes Campaign → Ad Set
            const key = `${campaign} → ${adset}`;

            const spend =
              parseFloat((r[spendIdx] || "0").replace(/[^0-9.]/g, "")) || 0;

            const clicks =
              parseFloat((r[clickIdx] || "0").replace(/[^0-9.]/g, "")) || 0;

            const reach =
              parseFloat((r[reachIdx] || "0").replace(/[^0-9.]/g, "")) || 0;

            const impressions =
              parseFloat((r[imprIdx] || "0").replace(/[^0-9.]/g, "")) || 0;

            if (!totals[key]) {
              totals[key] = {
                spend: 0,
                clicks: 0,
                reach: 0,
                impressions: 0,
              };
            }

            totals[key].spend += spend;
            totals[key].clicks += clicks;
            totals[key].reach += reach;
            totals[key].impressions += impressions;
          });

          // ✅ Compute CPC per Ad Set group
          Object.keys(totals).forEach((k) => {
            totals[k].cpc =
              totals[k].clicks > 0 ? totals[k].spend / totals[k].clicks : 0;
          });

          return totals;
        },

        getChannelAggregates: (id) => {
          const ch = DB[id];
          if (ch.metrics)
            return {
              ...ch.metrics,
              cpa:
                ch.metrics.leads.current > 0
                  ? ch.metrics.spend.current / ch.metrics.leads.current
                  : 0,
              roas:
                ch.metrics.spend.current > 0
                  ? ch.metrics.revenue.current / ch.metrics.spend.current
                  : 0,
              roi:
                ch.metrics.spend.current > 0
                  ? ((ch.metrics.revenue.current - ch.metrics.spend.current) /
                      ch.metrics.spend.current) *
                    100
                  : 9999,
            };
          return {
            spend: { current: 0 },
            leads: { current: 0 },
            revenue: { current: 0 },
            bookings: { current: 0 },
            cpa: 0,
            roas: 0,
            roi: 0,
          };
        },
        getGlobalStats: () => {
          let g = { revenue: 0, spend: 0, leads: 0, bookings: 0 };
          Object.keys(DB).forEach((k) => {
            if (DB[k].metrics && DB[k].type) {
              g.revenue += DB[k].metrics.revenue.current || 0;
              g.spend += DB[k].metrics.spend.current || 0;
              g.leads += DB[k].metrics.leads.current || 0;
              g.bookings += DB[k].metrics.bookings.current || 0;
            }
          });
          g.cpa = g.bookings > 0 ? g.spend / g.bookings : 0;
          g.avgDeal = g.bookings > 0 ? g.revenue / (g.bookings * 0.2) : 0;
          return g;
        },
      };

      // 3. UI RENDERER
      const UI = {
        usd: (n) =>
          new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          }).format(n || 0),

        num: (n) => new Intl.NumberFormat("en-US").format(n || 0),

        openGeniusModal: (channelId, optIndex) => {
          const ch = DB[channelId];
          const opt = ch.optimizations[optIndex];
          document.getElementById("taskModal").classList.remove("hidden");
          document.getElementById("modal-body").innerHTML = `
                                                                                              <div class="p-8">
                                                                                                <h4 class="text-2xl font-black text-gray-900 mb-2">${
                                                                                                  opt.title
                                                                                                }</h4>
                                                                                                <p class="text-sm text-gray-600 mb-4"><strong>Root:</strong> ${
                                                                                                  opt.rootCause
                                                                                                }</p>
                                                                                                <div class="prose text-sm text-gray-600">${opt.instruction.replace(
                                                                                                  /\n/g,
                                                                                                  "<br>",
                                                                                                )}</div>
                                                                                              </div>
                                                                                            `;
        },

        renderGlobal: () => {
          const g = Logic.getGlobalStats();
          return `
                                                                                              <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                                                                                                ${UI.card(
                                                                                                  "Total Revenue",
                                                                                                  UI.usd(
                                                                                                    g.revenue,
                                                                                                  ),
                                                                                                  "text-emerald-600",
                                                                                                  "Gross Revenue",
                                                                                                )}
                                                                                                ${UI.card(
                                                                                                  "Total Spend",
                                                                                                  UI.usd(
                                                                                                    g.spend,
                                                                                                  ),
                                                                                                  "text-slate-600",
                                                                                                  "Ad Spend",
                                                                                                )}
                                                                                                ${UI.card(
                                                                                                  "Blended CAQ",
                                                                                                  UI.usd(
                                                                                                    g.cpa,
                                                                                                  ),
                                                                                                  "text-orange-600",
                                                                                                  "Cost per Booking",
                                                                                                )}
                                                                                                ${UI.card(
                                                                                                  "Pipeline Volume",
                                                                                                  UI.num(
                                                                                                    g.leads,
                                                                                                  ),
                                                                                                  "text-gray-900",
                                                                                                  "Total Leads",
                                                                                                )}
                                                                                                ${UI.card(
                                                                                                  "Avg Deal Size",
                                                                                                  UI.usd(
                                                                                                    g.avgDeal,
                                                                                                  ),
                                                                                                  "text-blue-600",
                                                                                                  "Est. Value",
                                                                                                )}
                                                                                              </div>

                                                                                              <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                                                                                                <div class="lg:col-span-2 glass-panel p-6">
                                                                                                  <h3 class="font-bold text-gray-700 mb-4">Global Revenue Velocity</h3>
                                                                                                  <div class="h-72"><canvas id="mainChart"></canvas></div>
                                                                                                </div>
                                                                                                <div class="bg-slate-800 text-white p-6 rounded-xl shadow-lg border-l-4 border-orange-600">
                                                                                                  <h3 class="font-bold text-slate-400 text-xs uppercase tracking-widest mb-6">AI Feed</h3>
                                                                                                  <div class="space-y-4 text-sm">
                                                                                                    ${UI.feedItem(
                                                                                                      "Facebook",
                                                                                                      "good",
                                                                                                      "Scale Warning: ROI > 400%",
                                                                                                      "Approve",
                                                                                                    )}
                                                                                                    ${UI.feedItem(
                                                                                                      "Google",
                                                                                                      "bad",
                                                                                                      "CAQ spiked to $190",
                                                                                                      "Fix",
                                                                                                    )}
                                                                                                  </div>
                                                                                                </div>
                                                                                              </div>
                                                                                            `;
        },

        renderGoals: () => {
          const g = DB.goals;
          return `
                                                                                              <div class="glass-panel p-8">
                                                                                                <h2 class="text-2xl font-black text-gray-900 mb-2">Goals</h2>
                                                                                                <p class="text-gray-600 mb-6">${g.vision}</p>
                                                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                                                                  <div class="glass-panel p-6">
                                                                                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Appointments</p>
                                                                                                    <p class="text-4xl font-black text-gray-900">${g.targets.appointments.current}</p>
                                                                                                    <p class="text-sm text-gray-500">Target: ${g.targets.appointments.target}</p>
                                                                                                  </div>
                                                                                                  <div class="glass-panel p-6">
                                                                                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">CAQ</p>
                                                                                                    <p class="text-4xl font-black text-emerald-600">$${g.targets.caq.current}</p>
                                                                                                    <p class="text-sm text-gray-500">Target: under $${g.targets.caq.target}</p>
                                                                                                  </div>
                                                                                                </div>
                                                                                              </div>
                                                                                            `;
        },

        renderKnowledge: () => {
          const lib = DB.library;
          const tab = window.currentTab || "algo";

          const tabs = `
                                                                                              <div class="flex gap-4 border-b border-gray-200 mb-8">
                                                                                                <button onclick="window.currentTab='algo'; App.router('knowledge')" class="tab-btn ${
                                                                                                  tab ===
                                                                                                  "algo"
                                                                                                    ? "active"
                                                                                                    : ""
                                                                                                }">Algorithms</button>
                                                                                                <button onclick="window.currentTab='sops'; App.router('knowledge')" class="tab-btn ${
                                                                                                  tab ===
                                                                                                  "sops"
                                                                                                    ? "active"
                                                                                                    : ""
                                                                                                }">SOP Library</button>
                                                                                                <button onclick="window.currentTab='scripts'; App.router('knowledge')" class="tab-btn ${
                                                                                                  tab ===
                                                                                                  "scripts"
                                                                                                    ? "active"
                                                                                                    : ""
                                                                                                }">Script Vault</button>
                                                                                              </div>
                                                                                            `;

          let content = "";

          if (tab === "algo") {
            content = `<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">${lib.algorithms
              .map(
                (a, i) => `
                                                                                                    <div class="glass-panel p-6 border-t-4 ${a.color} kb-card" onclick="document.getElementById('algo-details-${i}').classList.toggle('hidden'); this.classList.toggle('expanded')">
                                                                                                      <div class="flex items-center gap-3 mb-4 border-b border-gray-100 pb-4">
                                                                                                        <i class="${a.icon} text-2xl text-gray-600"></i>
                                                                                                        <div>
                                                                                                          <h4 class="font-bold text-gray-900 text-lg m-0">${a.title}</h4>
                                                                                                          <p class="text-xs text-gray-500">${a.summary}</p>
                                                                                                        </div>
                                                                                                      </div>
                                                                                                      <div id="algo-details-${i}" class="hidden prose text-sm text-gray-600 border-t border-gray-100 pt-4 mt-2 bg-gray-50 p-4 rounded-lg">${a.details}</div>
                                                                                                      <p class="text-center text-xs text-orange-600 font-bold mt-2">Click to Expand</p>
                                                                                                    </div>
                                                                                                  `,
              )
              .join("")}</div>`;
          }

          if (tab === "sops") {
            content = `<div class="space-y-4">${lib.sops
              .map(
                (s, i) => `
                                                                                                    <div class="glass-panel p-6">
                                                                                                      <div class="flex items-center gap-3 mb-2">
                                                                                                        <div class="w-8 h-8 rounded bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold text-sm">${
                                                                                                          i +
                                                                                                          1
                                                                                                        }</div>
                                                                                                        <div>
                                                                                                          <p class="font-bold text-gray-900">${
                                                                                                            s.title
                                                                                                          }</p>
                                                                                                          <span class="tag bg-gray-100 text-gray-500 mt-1 inline-block">${
                                                                                                            s.category
                                                                                                          }</span>
                                                                                                        </div>
                                                                                                      </div>
                                                                                                      <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2">${s.steps
                                                                                                        .map(
                                                                                                          (
                                                                                                            step,
                                                                                                          ) =>
                                                                                                            `<li>${step}</li>`,
                                                                                                        )
                                                                                                        .join(
                                                                                                          "",
                                                                                                        )}</ol>
                                                                                                      <div class="mt-3 bg-blue-50 p-3 rounded border border-blue-100 text-sm text-blue-800">${
                                                                                                        s.details
                                                                                                      }</div>
                                                                                                    </div>
                                                                                                  `,
              )
              .join("")}</div>`;
          }

          if (tab === "scripts") {
            content = `<div class="grid grid-cols-1 md:grid-cols-2 gap-6">${lib.scripts
              .map(
                (s, i) => `
                                                                                                    <div class="glass-panel p-6">
                                                                                                      <p class="font-bold text-gray-900 mb-1">${s.title}</p>
                                                                                                      <p class="text-xs text-gray-500 italic mb-3">${s.context}</p>
                                                                                                      <p class="font-mono text-sm text-gray-700 bg-orange-50 p-3 rounded border border-orange-100 mb-3">"${s.content}"</p>
                                                                                                      <button onclick="document.getElementById('obj-${i}').classList.toggle('hidden')" class="text-xs font-bold text-gray-500 hover:text-orange-600">
                                                                                                        View Objection Handler
                                                                                                      </button>
                                                                                                      <div id="obj-${i}" class="hidden mt-2 p-3 bg-red-50 text-red-800 text-xs rounded border border-red-100">${s.objections}</div>
                                                                                                    </div>
                                                                                                  `,
              )
              .join("")}</div>`;
          }

          return `<div class="mb-8">
                                                                                                <h2 class="text-3xl font-black text-gray-900">Knowledge Hub</h2>
                                                                                                <p class="text-gray-500">Operational Intelligence</p>
                                                                                              </div>
                                                                                              ${tabs}
                                                                                              ${content}
                                                                                            `;
        },

        // Base channel render (NON-FACEBOOK)
        renderChannel: (id) => {
          const ch = DB[id];
          const m = ch.metrics || {
            spend: { current: 0, prev: 0 },
            leads: { current: 0, prev: 0 },
            bookings: { current: 0, prev: 0 },
            revenue: { current: 0, prev: 0 },
          };

          const d = (c, p) => {
            if (!p)
              return `<span class="text-gray-400 text-[10px] ml-2">—</span>`;
            const pct = ((c - p) / p) * 100;
            return `<span class="${
              pct >= 0 ? "text-emerald-500" : "text-red-500"
            } text-[10px] font-bold ml-2">${pct.toFixed(1)}%</span>`;
          };

          return `
                                                                                              <div class="flex justify-between items-center mb-6">
                                                                                                <div>
                                                                                                  <h2 class="text-2xl font-black text-gray-800">${
                                                                                                    ch.name
                                                                                                  }</h2>
                                                                                                  <p class="text-xs text-gray-500">Channel Intelligence</p>
                                                                                                </div>
                                                                                                <div class="text-xs font-bold text-gray-600 bg-white px-3 py-1.5 rounded border">Last 30 Days</div>
                                                                                              </div>

                                                                                              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                                                                                                ${UI.card(
                                                                                                  "Ad Spend",
                                                                                                  UI.usd(
                                                                                                    m
                                                                                                      .spend
                                                                                                      .current,
                                                                                                  ),
                                                                                                  d(
                                                                                                    m
                                                                                                      .spend
                                                                                                      .current,
                                                                                                    m
                                                                                                      .spend
                                                                                                      .prev,
                                                                                                  ),
                                                                                                )}
                                                                                                ${UI.card(
                                                                                                  "Leads",
                                                                                                  UI.num(
                                                                                                    m
                                                                                                      .leads
                                                                                                      .current,
                                                                                                  ),
                                                                                                  d(
                                                                                                    m
                                                                                                      .leads
                                                                                                      .current,
                                                                                                    m
                                                                                                      .leads
                                                                                                      .prev,
                                                                                                  ),
                                                                                                )}
                                                                                                ${UI.card(
                                                                                                  "Bookings",
                                                                                                  UI.num(
                                                                                                    m
                                                                                                      .bookings
                                                                                                      .current,
                                                                                                  ),
                                                                                                  d(
                                                                                                    m
                                                                                                      .bookings
                                                                                                      .current,
                                                                                                    m
                                                                                                      .bookings
                                                                                                      .prev,
                                                                                                  ),
                                                                                                )}
                                                                                                ${UI.card(
                                                                                                  "Revenue",
                                                                                                  UI.usd(
                                                                                                    m
                                                                                                      .revenue
                                                                                                      .current,
                                                                                                  ),
                                                                                                  d(
                                                                                                    m
                                                                                                      .revenue
                                                                                                      .current,
                                                                                                    m
                                                                                                      .revenue
                                                                                                      .prev,
                                                                                                  ),
                                                                                                )}
                                                                                              </div>

                                                                                              <div class="glass-panel p-6">
                                                                                                <h3 class="font-bold text-gray-700 mb-4">Efficiency Trend</h3>
                                                                                                <div class="h-64"><canvas id="channelChart"></canvas></div>
                                                                                              </div>
                                                                                            `;
        },

        // FACEBOOK FULL RENDER (with report selector + date filter)
        renderFacebookFull: (reportsIndex) => {
          const ch = DB.facebook;
          const m = ch.metrics;

          const safeIndex = Array.isArray(reportsIndex) ? reportsIndex : [];

          const options = safeIndex
            .map(
              (r) =>
                `<option value="${r.id}">${r.report_start} → ${r.report_end}</option>`,
            )
            .join("");

          const hasData = ch.csvHeaders.length && ch.csvRows.length;

          return `
                                                                                              <div class="flex justify-between items-center mb-6">
                                                                                                <div>
                                                                                                  <h2 class="text-2xl font-black text-gray-800">Facebook Ads</h2>
                                                                                                  <p class="text-xs text-gray-500">Reports + Filters</p>
                                                                                                </div>
                                                                                                <div class="text-xs font-bold text-gray-600 bg-white px-3 py-1.5 rounded border">
                                                                                                  Loaded: ${
                                                                                                    App
                                                                                                      .state
                                                                                                      .facebookReportRangeLabel ||
                                                                                                    "Latest"
                                                                                                  }
                                                                                                </div>
                                                                                              </div>

                                                                                              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                                                                                                <div class="glass-panel p-5">
                                                                                                  <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Ad Spend</p>
                                                                                                  <p class="text-3xl font-black text-slate-800">${UI.usd(
                                                                                                    m
                                                                                                      .spend
                                                                                                      .current,
                                                                                                  )}</p>
                                                                                                </div>
                                                                                                <div class="glass-panel p-5">
                                                                                                  <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Results</p>
                                                                                                  <p class="text-3xl font-black text-slate-800">${UI.num(
                                                                                                    m
                                                                                                      .bookings
                                                                                                      .current,
                                                                                                  )}</p>
                                                                                                </div>
                                                                                                <div class="glass-panel p-5">
                                                                                                  <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Cost Per Result</p>
                                                                                                  <p class="text-3xl font-black text-slate-800">${UI.usd(
                                                                                                    m
                                                                                                      .costPerResult
                                                                                                      .current,
                                                                                                  )}</p>
                                                                                                </div>
                                                                                              </div>

                                                                                              <div class="glass-panel p-6 mb-6">
                                                                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">
                                                                                                  Compare Date Ranges
                                                                                                </p>

                                                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                                                                                  <!-- ✅ CURRENT RANGE -->
                                                                                                  <div>
                                                                                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">
                                                                                                      Current Period
                                                                                                    </p>
                                                                                                    <div class="flex gap-2">
                                                                                                      <input
                                                                                                        id="fbStartDate"
                                                                                                        type="date"
                                                                                                        class="border rounded px-3 py-2 text-xs bg-white w-full"
                                                                                                      />
                                                                                                      <input
                                                                                                        id="fbEndDate"
                                                                                                        type="date"
                                                                                                        class="border rounded px-3 py-2 text-xs bg-white w-full"
                                                                                                      />
                                                                                                    </div>
                                                                                                  </div>

                                                                                                  <!-- ✅ PREVIOUS RANGE -->
                                                                                                  <div>
                                                                                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">
                                                                                                      Previous Period
                                                                                                    </p>
                                                                                                    <div class="flex gap-2">
                                                                                                      <input
                                                                                                        id="fbPrevStartDate"
                                                                                                        type="date"
                                                                                                        class="border rounded px-3 py-2 text-xs bg-white w-full"
                                                                                                      />
                                                                                                      <input
                                                                                                        id="fbPrevEndDate"
                                                                                                        type="date"
                                                                                                        class="border rounded px-3 py-2 text-xs bg-white w-full"
                                                                                                      />
                                                                                                    </div>
                                                                                                  </div>
                                                                                                </div>

                                                                                                <div class="mt-5 flex gap-3">
                                                                                                  <button
                                                                                                    onclick="App.applyFacebookComparison()"
                                                                                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-xs font-bold"
                                                                                                  >
                                                                                                    Apply Comparison
                                                                                                  </button>

                                                                                                  <button
                                                                                                    onclick="App.resetFacebookDateFilter()"
                                                                                                    class="bg-white border px-4 py-2 rounded text-xs font-bold"
                                                                                                  >
                                                                                                    Reset
                                                                                                  </button>
                                                                                                </div>
                                                                                              </div>


                                                                                              <!-- ✅ Campaign Efficiency Chart -->
                                                                                                <div class="glass-panel p-6 mb-6">
                                                                                                  <div class="flex justify-between items-center mb-4">
                                                                                                    <h3 class="font-bold text-gray-700">
                                                                                                      Campaign Efficiency Breakdown
                                                                                                    </h3>

                                                                                                    <div class="flex gap-3 items-center">
                                                                                                      <!-- ✅ Campaign Selector -->
                                                                                                      <div
                                                                                                        id="fbCampaignSelector"
                                                                                                        class="flex flex-wrap gap-2 p-3 border rounded bg-white w-[420px]"
                                                                                                      ></div>

                                                                                                      <!-- ✅ Compare Mode -->
                                                                                                      <select
                                                                                                        id="fbCompareMode"
                                                                                                        onchange="App.renderCampaignChart()"
                                                                                                        class="border rounded px-3 py-2 text-xs bg-white"
                                                                                                      >
                                                                                                        <option value="current">Current Period</option>
                                                                                                        <option value="previous">Previous Period</option>
                                                                                                        <option value="both">Compare Both</option>
                                                                                                      </select>
                                                                                                    </div>
                                                                                                  </div>

                                                                                                 <!-- ✅ CURRENT PERIOD CHART -->
                                                                                                    <div class="glass-panel p-6 mb-6">
                                                                                                      <h3 class="font-bold text-gray-700 mb-3">
                                                                                                        Current Period (${App.state.facebookCurrentLabel})
                                                                                                      </h3>
                                                                                                      <div class="h-[340px]">
                                                                                                        <canvas id="campaignChartCurrent"></canvas>
                                                                                                      </div>
                                                                                                    </div>

                                                                                                    <!-- ✅ PREVIOUS PERIOD CHART -->
                                                                                                    <div class="glass-panel p-6 mb-6">
                                                                                                      <h3 class="font-bold text-gray-700 mb-3">
                                                                                                        Previous Period (${App.state.facebookPreviousLabel})
                                                                                                      </h3>
                                                                                                      <div class="h-[340px]">
                                                                                                        <canvas id="campaignChartPrevious"></canvas>
                                                                                                      </div>
                                                                                                    </div>

                                                                                                  <p
                                                                                                    id="noSelectionMsg"
                                                                                                    class="hidden text-sm text-gray-500 mt-3 text-center"
                                                                                                  >
                                                                                                    Select at least one campaign to display the graph.
                                                                                                  </p>
                                                                                                </div>
                                                                                              ${
                                                                                                hasData
                                                                                                  ? `
                                                                                                    <div class="glass-panel overflow-hidden">
                                                                                                      <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                                                                                                        <h3 class="font-bold text-gray-700">Facebook Ads Report</h3>
                                                                                                        <span class="text-xs text-gray-500">Rows: ${
                                                                                                          ch
                                                                                                            .csvRows
                                                                                                            .length
                                                                                                        }</span>
                                                                                                      </div>
                                                                                                      <div class="overflow-x-auto">
                                                                                                        <table class="w-full text-xs facebook-table">
                                                                                                          <thead class="facebook-table">
                                                                                                            <tr>${ch.csvHeaders
                                                                                                              .map(
                                                                                                                (
                                                                                                                  h,
                                                                                                                ) =>
                                                                                                                  `<th>${h}</th>`,
                                                                                                              )
                                                                                                              .join(
                                                                                                                "",
                                                                                                              )}</tr>
                                                                                                          </thead>
                                                                                                          <tbody>
                                                                                                            ${ch.csvRows
                                                                                                              .map(
                                                                                                                (
                                                                                                                  row,
                                                                                                                ) => `
                                                                                                                <tr>
                                                                                                                  ${row
                                                                                                                    .map(
                                                                                                                      (
                                                                                                                        cell,
                                                                                                                      ) =>
                                                                                                                        `<td>${
                                                                                                                          cell ||
                                                                                                                          "-"
                                                                                                                        }</td>`,
                                                                                                                    )
                                                                                                                    .join(
                                                                                                                      "",
                                                                                                                    )}
                                                                                                                </tr>
                                                                                                              `,
                                                                                                              )
                                                                                                              .join(
                                                                                                                "",
                                                                                                              )}
                                                                                                          </tbody>
                                                                                                        </table>
                                                                                                      </div>
                                                                                                    </div>
                                                                                                  `
                                                                                                  : `<div class="glass-panel p-6 text-gray-500">No Facebook data loaded yet. Click “Upload Data” to add a CSV.</div>`
                                                                                              }
                                                                                            `;
        },

        card: (t, v, d, e = "") =>
          `<div class="glass-panel p-5 ${e}">
                                                                                              <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">${t}</p>
                                                                                              <div class="flex items-baseline gap-2 mt-1">
                                                                                                <h3 class="text-2xl font-black text-slate-800">${v}</h3>
                                                                                              </div>
                                                                                              <div class="mt-1">${
                                                                                                d ||
                                                                                                ""
                                                                                              }</div>
                                                                                            </div>`,

        feedItem: (s, t, m, a) =>
          `<div class="border-b border-gray-700/50 pb-3 last:border-0 group">
                                                                                              <div class="flex justify-between items-center mb-1">
                                                                                                <span class="text-xs font-bold text-gray-300 flex items-center">
                                                                                                  <span class="w-2 h-2 rounded-full ${
                                                                                                    t ===
                                                                                                    "good"
                                                                                                      ? "bg-emerald-500"
                                                                                                      : "bg-red-500"
                                                                                                  } inline-block mr-2"></span>${s}
                                                                                                </span>
                                                                                                <button class="text-[9px] bg-orange-600 text-white px-2 rounded opacity-80 hover:opacity-100">${a}</button>
                                                                                              </div>
                                                                                              <p class="text-xs text-slate-400 pl-4">${m}</p>
                                                                                            </div>`,
      };

      // ===============================
      // APP (ONE CLEAN OBJECT)
      // ===============================
      const App = {
        campaignChartInstance: null,
        state: {
          facebookReportId: null,
          facebookReportRangeLabel: null,
          facebookCurrentLabel: "Current Period",
          facebookPreviousLabel: "Previous Period",
          currentRoute: "global",
          filteredFacebookReports: null,
          facebookForceRender: false,
          facebookDateFilterActive: false,
        },
        renderCampaignChart: () => {
          const headers = DB.facebook.csvHeaders;
          if (!headers.length) return;

          const selector = document.getElementById("fbCampaignSelector");
          if (!selector) return;

          // ✅ Selected campaigns
          const selectedCampaigns = Array.from(
            selector.querySelectorAll("input[type='checkbox']:checked"),
          ).map((cb) => cb.value);

          if (!selectedCampaigns.length) return;

          // ✅ Helper renderer
          const drawChart = (canvasId, rows) => {
            if (!rows.length) return;

            const campaignData = Logic.aggregateCampaignMetrics(headers, rows);

            let campaigns = Object.keys(campaignData).filter((c) =>
              selectedCampaigns.includes(c),
            );

            if (!campaigns.length) return;

            campaigns.sort(
              (a, b) => campaignData[b].spend - campaignData[a].spend,
            );

            const topCampaigns = campaigns.slice(0, 10);

            const spendVals = topCampaigns.map((c) =>
              campaignData[c].spend.toFixed(2),
            );
            const cpcVals = topCampaigns.map((c) =>
              campaignData[c].cpc.toFixed(2),
            );
            const reachVals = topCampaigns.map((c) =>
              campaignData[c].reach.toFixed(0),
            );
            const imprVals = topCampaigns.map((c) =>
              campaignData[c].impressions.toFixed(0),
            );

            const ctx = document.getElementById(canvasId)?.getContext("2d");
            if (!ctx) return;

            return new Chart(ctx, {
              type: "bar",
              data: {
                labels: topCampaigns.map((c) =>
                  c.length > 35 ? c.slice(0, 35) + "..." : c,
                ),
                datasets: [
                  { label: "Spend ($)", data: spendVals },
                  { label: "CPC ($)", data: cpcVals },
                  { label: "Reach", data: reachVals },
                  { label: "Impressions", data: imprVals },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                  legend: { position: "top" },

                  tooltip: {
                    enabled: true,
                  },

                  datalabels: {
                    anchor: "end",
                    align: "top",
                    formatter: function (value, context) {
                      // ✅ Detect dataset label
                      const label = context.dataset.label;

                      // ✅ Add $ for Spend + CPC
                      if (label.includes("Spend") || label.includes("CPC")) {
                        return "$" + Number(value).toFixed(2);
                      }

                      // ✅ Reach + Impressions stay normal
                      return Number(value).toLocaleString();
                    },

                    font: {
                      weight: "bold",
                      size: 10,
                    },
                  },
                },
              },
            });
          };

          // ✅ Destroy previous chart objects
          App.currentChart?.destroy();
          App.previousChart?.destroy();

          // ✅ Draw current + previous stacked
          App.currentChart = drawChart(
            "campaignChartCurrent",
            DB.facebook.currentRows || [],
          );

          App.previousChart = drawChart(
            "campaignChartPrevious",
            DB.facebook.previousRows || [],
          );
        },

        populateCampaignSelector: () => {
          const headers = DB.facebook.csvHeaders;
          const rows = DB.facebook.csvRows;

          if (!headers.length || !rows.length) return;

          const campaignData = Logic.aggregateCampaignMetrics(headers, rows);
          const campaigns = Object.keys(campaignData);

          const container = document.getElementById("fbCampaignSelector");
          if (!container) return;

          container.innerHTML = "";

          campaigns.forEach((c) => {
            // ✅ Extract short ID like DFY-BKN437
            const shortName = c.match(/DFY-BKN\d+/)?.[0] || c.slice(0, 15);

            const wrapper = document.createElement("label");

            wrapper.className =
              "flex items-center gap-1 px-3 py-1 rounded-full border text-xs font-semibold cursor-pointer hover:bg-orange-50";

            wrapper.innerHTML = `
    <input
      type="checkbox"
      value="${c}"
      checked
      onchange="App.renderCampaignChart()"
      class="accent-orange-600 w-3 h-3"
    />
    <span class="text-gray-700">${shortName}</span>
  `;

            container.appendChild(wrapper);
          });
        },
        // ===============================
        filterFacebookReportsByDate: (reports, start, end) => {
          const startD = new Date(start + "T00:00:00");
          const endD = new Date(end + "T23:59:59");

          return reports.filter((r) => {
            const rStart = new Date(r.report_start + "T00:00:00");
            const rEnd = new Date(r.report_end + "T23:59:59");
            return rStart <= endD && rEnd >= startD;
          });
        },
        applyFacebookComparison: async () => {
          const start = document.getElementById("fbStartDate").value;
          const end = document.getElementById("fbEndDate").value;

          const pStart = document.getElementById("fbPrevStartDate").value;
          const pEnd = document.getElementById("fbPrevEndDate").value;

          if (!start || !end || !pStart || !pEnd) {
            alert("Pick both CURRENT and PREVIOUS date ranges.");
            return;
          }

          // ✅ Fetch Current Period
          const resCurrent = await fetch(
            api(`/api/get-facebook-report-range.php?start=${start}&end=${end}`),
          );
          const jsonCurrent = await resCurrent.json();

          // ✅ Fetch Previous Period
          const resPrev = await fetch(
            api(
              `/api/get-facebook-report-range.php?start=${pStart}&end=${pEnd}`,
            ),
          );
          const jsonPrev = await resPrev.json();

          if (
            !Array.isArray(jsonCurrent.rows) ||
            !Array.isArray(jsonPrev.rows)
          ) {
            alert("Invalid API response.");
            return;
          }

          DB.facebook.csvHeaders = jsonCurrent.headers || [];

          // ✅ Save BOTH datasets
          DB.facebook.currentRows = jsonCurrent.rows;
          DB.facebook.previousRows = jsonPrev.rows;

          // ✅ Default table shows Current
          DB.facebook.csvRows = DB.facebook.currentRows;

          // ✅ Update labels
          App.state.facebookCurrentLabel = `${start} → ${end}`;
          App.state.facebookPreviousLabel = `${pStart} → ${pEnd}`;

          App.router("facebook");
        },
        applyFacebookReportDateFilter: async () => {
          const start = document.getElementById("fbStartDate").value;
          const end = document.getElementById("fbEndDate").value;

          if (!start || !end) {
            alert("Pick both start and end dates.");
            return;
          }

          const res = await fetch(
            api(`/api/get-facebook-report-range.php?start=${start}&end=${end}`),
          );

          if (!res.ok) {
            alert("Failed to load date range.");
            return;
          }

          const json = await res.json();

          if (!Array.isArray(json.rows)) {
            console.error("Invalid API response:", json);
            alert("Unexpected response from server.");
            return;
          }

          if (!json.rows || !json.rows.length) {
            alert("No data found for selected dates.");
            return;
          }

          // Ensure headers are set
          if (Array.isArray(json.headers)) {
            DB.facebook.csvHeaders = json.headers;
          }

          // Set rows
          DB.facebook.csvRows = json.rows;
          DB.facebook._allRows = json.rows.slice();

          App.state.facebookDateFilterActive = true;
          App.state.facebookReportRangeLabel = `${start} → ${end}`;

          App.recalculateFacebookKPIs(
            DB.facebook.csvHeaders,
            DB.facebook.csvRows,
          );

          App.router("facebook");
        },

        init: async () => {
          App.router("global");
        },

        router: async (route) => {
          App.state.currentRoute = route;

          document
            .querySelectorAll(".nav-item")
            .forEach((el) => el.classList.remove("active"));
          const nav = document.getElementById(`nav-${route}`);
          if (nav) nav.classList.add("active");

          const container = document.getElementById("content-area");

          if (route === "global") {
            document.getElementById("page-title").innerText = "Global Command";
            document.getElementById("page-subtitle").innerText =
              "Enterprise View";
            container.innerHTML = UI.renderGlobal();

            setTimeout(() => {
              const mainChart = document.getElementById("mainChart");
              if (mainChart) {
                new Chart(mainChart, {
                  type: "line",
                  data: {
                    labels: ["M", "T", "W", "T", "F", "S", "S"],
                    datasets: [
                      {
                        data: [65, 59, 80, 81, 56, 95, 110],
                        borderColor: "#ea580c",
                        backgroundColor: "#ea580c10",
                        fill: true,
                      },
                    ],
                  },
                  options: {
                    maintainAspectRatio: false,
                    plugins: { legend: false },
                    scales: { y: { display: false }, x: { display: false } },
                  },
                });
              }
            }, 50);

            return;
          }

          if (route === "goals") {
            document.getElementById("page-title").innerText =
              "Goals & Alignment";
            document.getElementById("page-subtitle").innerText =
              "Targets & Scorecard";
            container.innerHTML = UI.renderGoals();
            return;
          }

          if (route === "knowledge") {
            document.getElementById("page-title").innerText =
              "Information Center";
            document.getElementById("page-subtitle").innerText =
              "Knowledge Hub";
            container.innerHTML = UI.renderKnowledge();
            return;
          }

          // FACEBOOK route uses async render (reports index)
          if (route === "facebook") {
            document.getElementById("page-title").innerText = "Facebook Ads";
            document.getElementById("page-subtitle").innerText =
              "Reports & Filters";

            container.innerHTML = `<div class="glass-panel p-6 text-gray-500">Loading Facebook reports...</div>`;

            let monthData = null;

            if (!App.state.facebookDateFilterActive) {
              monthData = await App.loadLatestFacebookMonth();
            }

            container.innerHTML = UI.renderFacebookFull([]);

            setTimeout(() => {
              // ✅ Set date inputs after the UI is rendered to reflect the loaded range
              if (monthData && monthData.start && monthData.end) {
                  const startInput = document.getElementById('fbStartDate');
                  const endInput = document.getElementById('fbEndDate');
                  const pStartInput = document.getElementById('fbPrevStartDate');
                  const pEndInput = document.getElementById('fbPrevEndDate');

                  if(startInput) startInput.value = monthData.start;
                  if(endInput) endInput.value = monthData.end;

                  // Also pre-fill previous month for convenience
                  const prevMonth = new Date(monthData.start + 'T12:00:00');
                  prevMonth.setMonth(prevMonth.getMonth() - 1);
                  if(pStartInput) pStartInput.value = prevMonth.toISOString().split('T')[0].slice(0, -3) + '-01';
                  if(pEndInput) pEndInput.value = new Date(prevMonth.getFullYear(), prevMonth.getMonth() + 1, 0).toISOString().split('T')[0];
              }

              App.populateCampaignSelector(); // ✅ load names
              App.renderCampaignChart(); // ✅ draw chart
            }, 200);

            return;
          }

          // Other channels (non-async)
          const data = DB[route];
          if (data) {
            document.getElementById("page-title").innerText =
              data.name || "Channel";
            document.getElementById("page-subtitle").innerText =
              "Channel Intelligence";
            container.innerHTML = UI.renderChannel(route);

            setTimeout(() => {
              const channelChart = document.getElementById("channelChart");
              if (channelChart && data.history && data.history.roas) {
                new Chart(channelChart, {
                  type: "line",
                  data: {
                    labels: ["M", "T", "W", "T", "F", "S", "S"],
                    datasets: [
                      {
                        data: data.history.roas,
                        borderColor: "#ea580c",
                        backgroundColor: "#ea580c10",
                        fill: true,
                      },
                    ],
                  },
                  options: {
                    maintainAspectRatio: false,
                    plugins: { legend: false },
                    scales: { y: { display: false }, x: { display: false } },
                  },
                });
              }
            }, 50);
          } else {
            container.innerHTML = '<div class="p-10 text-center">No Data</div>';
          }
        },

        loadLatestFacebookMonth: async () => {
          const res = await fetch(api("/api/get-facebook-latest-month.php"));
          if (!res.ok) {
            alert("Failed to load latest Facebook data.");
            return;
          }

          const json = await res.json();

          if (!Array.isArray(json.rows) || !json.rows.length) {
            DB.facebook.csvRows = [];
            DB.facebook.currentRows = [];
            DB.facebook.previousRows = [];
            DB.facebook.csvHeaders = json.headers || [];
            App.state.facebookReportRangeLabel = "No data";
            return json;
          }

          DB.facebook.csvHeaders = json.headers;
          DB.facebook.csvRows = json.rows;
          DB.facebook.currentRows = json.rows; // Set current rows for chart
          DB.facebook.previousRows = []; // Clear previous rows
          DB.facebook._allRows = json.rows.slice();

          App.state.facebookReportRangeLabel = json.month;

          App.recalculateFacebookKPIs(json.headers, json.rows);
          return json;
        },

        resetFacebookDateFilter: () => {
          App.state.facebookDateFilterActive = false;

          App.loadLatestFacebookMonth().then(() => {
            App.router("facebook");

            setTimeout(() => {
              App.renderCampaignChart();
            }, 150);
          });
        },

        normalizeDateToYMD: (raw) => {
          if (!raw) return null;

          // Already YYYY-MM-DD
          if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return raw;

          // Handle MM/DD/YYYY
          const m = raw.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
          if (m) {
            const mm = String(m[1]).padStart(2, "0");
            const dd = String(m[2]).padStart(2, "0");
            const yyyy = m[3];
            return `${yyyy}-${mm}-${dd}`;
          }

          // Fallback: try Date parse but return LOCAL yyyy-mm-dd (no ISO)
          const d = new Date(raw);
          if (!isNaN(d.getTime())) {
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, "0");
            const dd = String(d.getDate()).padStart(2, "0");
            return `${yyyy}-${mm}-${dd}`;
          }

          return null;
        },

        // Meta exports sometimes use "2025-12-13" or "Dec 13, 2025"
        // parseFlexibleDate: (raw) => {
        //   if (!raw) return null;
        //   // Try native Date first
        //   const d1 = new Date(raw);
        //   if (!isNaN(d1.getTime())) return d1;

        //   // Try YYYY-MM-DD
        //   const m = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        //   if (m) {
        //     const d2 = new Date(`${m[1]}-${m[2]}-${m[3]}T00:00:00`);
        //     if (!isNaN(d2.getTime())) return d2;
        //   }

        //   return null;
        // },

        getRowDate: (headers, row) => {
          const normalize = (s) =>
            (s || "").toLowerCase().replace(/\s+/g, " ").trim();

          const candidates = [
            "day",
            "date",
            "reporting starts",
            "reporting ends",
          ];

          for (let i = 0; i < headers.length; i++) {
            const h = normalize(headers[i]);
            if (candidates.some((c) => h === c || h.includes(c))) {
              return App.normalizeDateToYMD(row[i]);
            }
          }

          return null;
        },

        // ===============================
        // FACEBOOK: KPI RECALC
        // ===============================
        recalculateFacebookKPIs: (headers, rows) => {
          if (!Array.isArray(headers) || !headers.length) {
            console.warn("KPI calc skipped: headers missing");
            return;
          }

          if (!Array.isArray(rows)) {
            console.warn("KPI calc skipped: rows missing");
            return;
          }
          const normalize = (s) =>
            (s || "").toLowerCase().replace(/\s+/g, " ").trim();

          const spendIdx = headers.findIndex((h) =>
            normalize(h).includes("amount spent"),
          );
          const resultsIdx = headers.findIndex(
            (h) => normalize(h) === "results",
          );

          const cprIdx = headers.findIndex((h) =>
            normalize(h).includes("cost per result"),
          );

          let spend = 0;
          let results = 0;
          let cprSum = 0;

          rows.forEach((r) => {
            spend +=
              parseFloat(
                (r[spendIdx] || "0").toString().replace(/[^0-9.]/g, ""),
              ) || 0;
            results +=
              parseInt(
                (r[resultsIdx] || "0").toString().replace(/[^0-9]/g, ""),
              ) || 0;
            cprSum +=
              parseFloat(
                (r[cprIdx] || "0").toString().replace(/[^0-9.]/g, ""),
              ) || 0;
          });

          DB.facebook.metrics.spend.current = spend;
          DB.facebook.metrics.bookings.current = results;
          DB.facebook.metrics.costPerResult.current =
            results > 0 ? spend / results : 0;
          DB.facebook.metrics.leads.current = 0;
          DB.facebook.metrics.revenue.current = 0;
        },

        // ===============================
        // FACEBOOK CSV UPLOAD (WORKING)
        // ===============================
        triggerFacebookUpload: () => {
          const input = document.getElementById("fbUpload");
          if (!input) {
            alert("Upload input not found");
            return;
          }
          input.value = "";
          input.click();
        },

        extractReportRangeFromCSV: (headers, rows) => {
          const normalize = (s) =>
            (s || "").toLowerCase().replace(/\s+/g, " ").trim();

          const startIdx = headers.findIndex(
            (h) => normalize(h) === "reporting starts",
          );
          const endIdx = headers.findIndex(
            (h) => normalize(h) === "reporting ends",
          );

          if (startIdx === -1 || endIdx === -1) return null;

          let minStart = null;
          let maxEnd = null;

          rows.forEach((r) => {
            const s = (r[startIdx] || "").trim();
            const e = (r[endIdx] || "").trim();

            if (!s || !e) return;

            // if the CSV is "12/1/2025", this won't be YYYY-MM-DD.
            // so we convert it safely:
            const start = App.normalizeDateToYMD(s);
            const end = App.normalizeDateToYMD(e);

            if (!start || !end) return;

            if (!minStart || start < minStart) minStart = start;
            if (!maxEnd || end > maxEnd) maxEnd = end;
          });

          if (!minStart || !maxEnd) return null;

          return { start: minStart, end: maxEnd };
        },

        processFacebookCSV: async (csvText, silent = false) => {
          const rows = csvText
            .split("\n")
            .filter((r) => r.trim().length)
            .map(parseCSVRow);

          if (!rows.length) return alert("CSV file is empty.");

          const headers = rows[0].map((h) => h.trim());
          const rawData = rows.slice(1);

          // ⬇️ NEW: enrich rows with normalized dates
          const enrichedRows = rawData
            .map((row) => {
              const reportDate = App.getRowDate(headers, row);
              if (!reportDate) return null;

              return {
                report_date: reportDate,
                row: row,
              };
            })
            .filter(Boolean);

          // Keep UI working as-is
          DB.facebook.csvHeaders = headers;
          DB.facebook.csvRows = rawData;
          DB.facebook._allRows = rawData.slice();

          const range = App.extractReportRangeFromCSV(headers, rawData);

          const res = await fetch(api("/api/save-facebook-report.php"), {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              report_start: range ? range.start : null,
              report_end: range ? range.end : null,
              headers: headers,
              rows: enrichedRows,
            }),
          });

          const result = await res.json();

          if (!res.ok || !result.success) {
            alert("Upload failed. Check console.");
            console.error(result);
            return result;
          }

          if (!silent) {
            if (result.inserted_days === 0) {
              alert(
                "⚠️ No new data added.\nAll dates in this file already exist.",
              );
            } else if (result.skipped_days > 0) {
              alert(
                `✅ Upload complete.\n` +
                  `${result.inserted_days} new day(s) added.\n` +
                  `${result.skipped_days} duplicate day(s) skipped.`,
              );
            } else {
              alert(
                `✅ Upload complete.\n${result.inserted_days} new day(s) added.`,
              );
            }
          }

          App.state.facebookReportId = null;
          App.state.facebookReportRangeLabel = "Latest";

          App.recalculateFacebookKPIs(headers, rawData);

          App.router("facebook");
          return result; // ✅ REQUIRED
        },
      };

      // ===============================
      // FACEBOOK: LOAD SAVED REPORT BY ID
      // ===============================
      // App.loadFacebookReportById = async (id) => {
      //   const res = await fetch(api(`/api/get-facebook-report.php?id=${id}`));
      //   if (!res.ok) {
      //     alert("Failed to load report (API error).");
      //     return;
      //   }

      //   let json;
      //   try {
      //     json = await res.json();
      //   } catch (e) {
      //     alert("Invalid response from server.");
      //     return;
      //   }

      //   if (!json || !json.rows) {
      //     alert("Failed to load selected report.");
      //     return;
      //   }

      //   // Overwrite Facebook state
      //   DB.facebook.csvHeaders = json.headers;
      //   DB.facebook.csvRows = json.rows;
      //   DB.facebook._allRows = json.rows.slice();

      //   App.state.facebookReportId = id;
      //   App.state.facebookReportRangeLabel = "Saved Report";

      //   App.recalculateFacebookKPIs(json.headers, json.rows);
      //   App.state.facebookForceRender = true;
      //   App.router("facebook");
      // };
      // ===============================
      // FILE INPUT EVENT
      // ===============================
      document.addEventListener("DOMContentLoaded", () => {
        const input = document.getElementById("fbUpload");

        if (input) {
          input.addEventListener("change", async (e) => {
            const files = Array.from(e.target.files);
            if (!files.length) return;

            let totalInserted = 0;
            let totalSkipped = 0;

            for (const file of files) {
              const text = await file.text();

              const result = await App.processFacebookCSV(text, true); // ✅ silent mode

              if (result?.inserted_days) totalInserted += result.inserted_days;
              if (result?.skipped_days) totalSkipped += result.skipped_days;
            }

            alert(
              `✅ Upload Complete\n\n` +
                `New Days Added: ${totalInserted}\n` +
                `Duplicates Skipped: ${totalSkipped}`,
            );

            await App.loadLatestFacebookMonth(); // ✅ refresh latest data
            App.router("facebook");
          });
        }

        App.init();
      });
    </script>

    <input type="file" id="fbUpload" accept=".csv" multiple class="hidden" />
  </body>
</html>
