<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RankMaid Ultimate | Client Success & Performance</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- AESTHETIC BASE: RANKMAID V8 --- */
        body { background-color: #f8fafc; color: #0f172a; font-family: 'Inter', system-ui, sans-serif; }
        
        /* Navigation */
        .nav-item { transition: all 0.2s; cursor: pointer; border-left: 3px solid transparent; }
        .nav-item:hover { background-color: #1e293b; color: white; }
        .nav-item.active { background-color: #0ea5e9; color: white; border-left: 3px solid #7dd3fc; }

        /* Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* --- DASHBOARD COMPONENTS --- */
        .master-table th { 
            position: sticky; top: 0; background: #f8fafc; z-index: 20; 
            font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; 
            padding: 12px 16px; border-bottom: 2px solid #e2e8f0; 
        }
        .master-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .master-table tr:hover { background-color: #f1f5f9; }
        
        /* Status Pills (V8 Style) */
        .pill { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 6px; cursor: pointer; user-select: none; transition: all 0.15s; border: 1px solid transparent; min-width: 80px; text-align: center; display: inline-block; }
        .pill-done { background: #dcfce7; color: #15803d; border-color: #86efac; }
        .pill-pending { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; }
        .pill-issue { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }

        /* --- DEEP DIVE COMPONENTS (V2 MERGE) --- */
        
        /* Tabs */
        .tab-btn { border-bottom: 2px solid transparent; color: #64748b; font-weight: 600; padding: 12px 24px; transition: all 0.2s; font-size: 13px; }
        .tab-btn:hover { color: #0ea5e9; }
        .tab-btn.active { border-bottom-color: #0ea5e9; color: #0ea5e9; }

        /* Kanban (Workflow) */
        .kanban-col { background: #f1f5f9; border-radius: 12px; padding: 16px; min-height: 500px; display: flex; flex-direction: column; gap: 12px; border: 1px solid #e2e8f0; }
        .kanban-header { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .kanban-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s; position: relative; }
        .kanban-card:hover { transform: translateY(-2px); box-shadow: 0 8px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; z-index: 10; }
        
        /* Tags */
        .tag-recurring { background: #e0f2fe; color: #0369a1; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-block; }
        .tag-csm { background: #fef9c3; color: #a16207; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-block; }
        .tag-oneoff { background: #f3f4f6; color: #475569; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-block; }
        .timer-active { color: #ef4444; animation: pulse 2s infinite; font-weight: bold; }

        /* Editor */
        .editor-container { background: white; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; flex-direction: column; height: 600px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .editor-toolbar { border-bottom: 1px solid #e2e8f0; padding: 12px; background: #f8fafc; border-radius: 8px 8px 0 0; display: flex; gap: 12px; color: #64748b; }
        .editor-content { flex: 1; padding: 24px; outline: none; overflow-y: auto; font-family: 'Georgia', serif; font-size: 16px; line-height: 1.6; color: #334155; }
        .entity-item { display: flex; justify-content: space-between; align-items: center; padding: 8px; border-radius: 6px; font-size: 12px; border: 1px solid transparent; }
        .entity-item.found { background-color: #f0fdf4; border-color: #bbf7d0; }

        /* SOP Library Styles */
        .sop-nav-item { padding: 10px 16px; font-size: 13px; color: #475569; border-radius: 6px; cursor: pointer; transition: all 0.2s; font-weight: 500; }
        .sop-nav-item:hover { background-color: #f1f5f9; color: #0f172a; }
        .sop-nav-item.active { background-color: #e0f2fe; color: #0369a1; font-weight: 600; }
        .sop-content h1 { font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 16px; }
        .sop-content h2 { font-size: 18px; font-weight: 700; color: #334155; margin-top: 24px; margin-bottom: 12px; }
        .sop-content h3 { font-size: 15px; font-weight: 700; color: #475569; margin-top: 16px; margin-bottom: 8px; }
        .sop-content p { color: #475569; line-height: 1.6; margin-bottom: 16px; }
        .sop-content ul { list-style-type: disc; padding-left: 20px; color: #475569; margin-bottom: 16px; }
        .sop-content li { margin-bottom: 8px; }
        .copy-block { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; position: relative; font-family: monospace; font-size: 12px; color: #334155; margin-bottom: 16px; white-space: pre-wrap; }
        .copy-btn { position: absolute; top: 8px; right: 8px; background: white; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 8px; font-size: 10px; font-weight: bold; color: #64748b; cursor: pointer; transition: all 0.2s; }
        .copy-btn:hover { border-color: #0ea5e9; color: #0ea5e9; }
        .alert-box { background-color: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 12px; border-radius: 6px; font-size: 12px; margin-bottom: 16px; }

        /* Animations */
        .fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }

        /* Strategy Banner */
        .strategy-banner { background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white; border-radius: 12px; padding: 24px; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
        .strategy-banner::after { content: ''; position: absolute; right: 0; top: 0; height: 100%; width: 300px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1)); pointer-events: none; }
    </style>
</head>
<body class="h-screen flex overflow-hidden text-sm">

    <aside class="w-64 bg-slate-900 text-slate-400 flex flex-col shadow-2xl z-20 flex-shrink-0 border-r border-slate-800">
        <div class="h-16 flex items-center px-6 bg-slate-950 border-b border-slate-800">
            <div class="w-8 h-8 rounded bg-sky-600 flex items-center justify-center text-white font-black mr-3 shadow-lg shadow-sky-900/50">R</div>
            <div>
                <h1 class="font-black text-white text-lg tracking-tight">RANKMAID</h1>
                <p class="text-[9px] uppercase font-bold text-sky-500 tracking-wider">Ultimate Edition</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-2 space-y-1">
            <div class="px-4 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Management</div>
            <div onclick="App.router('dashboard')" id="nav-dashboard" class="nav-item flex items-center px-4 py-3 rounded-lg text-sm font-medium active">
                <i class="fa-solid fa-layer-group w-5 mr-2 text-center"></i> <span>Master List</span>
            </div>
            <div onclick="App.router('watchlist')" id="nav-watchlist" class="nav-item flex items-center px-4 py-3 rounded-lg text-sm font-medium">
                <i class="fa-solid fa-fire w-5 mr-2 text-center"></i> <span>Issues Watchlist</span>
                <span id="issue-badge" class="ml-auto bg-red-600 text-white text-[9px] px-2 py-0.5 rounded-full font-bold hidden">0</span>
            </div>

            <div class="px-4 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Execution</div>
            <div onclick="App.router('outreach')" id="nav-outreach" class="nav-item flex items-center px-4 py-3 rounded-lg text-sm font-medium">
                <i class="fa-solid fa-bullhorn w-5 mr-2 text-center"></i> <span>Outreach Gen</span>
            </div>
            <div onclick="App.router('sops')" id="nav-sops" class="nav-item flex items-center px-4 py-3 rounded-lg text-sm font-medium">
                <i class="fa-solid fa-book-open w-5 mr-2 text-center"></i> <span>SOP Library</span>
            </div>
        </nav>

        <div class="p-4 bg-slate-950 border-t border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-white text-xs font-bold relative">
                    G
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-white">Gene (Manager)</div>
                    <div class="text-[10px] text-emerald-500 font-medium">System Online</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative h-full bg-[#F8FAFC]">
        
        <header class="h-16 bg-white border-b border-gray-200 flex justify-between items-center px-8 z-10 shadow-sm flex-shrink-0">
            <div>
                <h2 id="page-title" class="text-xl font-bold text-slate-800">Master Dashboard</h2>
                <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Tracking <strong id="total-clients" class="text-slate-900">0</strong> Active Accounts</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                    <button onclick="App.toggleViewMode('ops')" id="view-ops" class="px-4 py-1.5 text-xs font-bold rounded shadow-sm bg-white text-slate-800 transition-all">Ops View</button>
                    <button onclick="App.toggleViewMode('kpi')" id="view-kpi" class="px-4 py-1.5 text-xs font-bold rounded text-slate-500 hover:text-slate-800 transition-all">KPI View</button>
                </div>
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    <input type="text" id="global-search" onkeyup="App.filterDashboard()" placeholder="Search clients..." 
                           class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-xs w-64 focus:ring-2 focus:ring-sky-500 outline-none transition-all shadow-sm">
                </div>
            </div>
        </header>

        <div id="content-area" class="flex-1 overflow-y-auto p-8 fade-in relative">
            </div>

    </main>

    <script>
        // --- DATA LAYER ---
        const DB = {
            currentUser: 'Gene',
            viewMode: 'ops', // 'ops' or 'kpi'
            clients: [],
            gscAuthUrl: null,
            gscSetupMessage: null,
            
            // --- HYPER-ADVANCED 2026 REI SOP LIBRARY ---
            sopLibrary: {
                categories: [
                    { id: 'geo', name: '1. SGE & AI Optimization' },
                    { id: 'local', name: '2. Local Signals (Map Pack)' },
                    { id: 'content', name: '3. Content Architecture' },
                    { id: 'tech', name: '4. Tech & Schema' },
                    { id: 'eeat', name: '5. E-E-A-T & Trust' }
                ],
                articles: [
                    // CATEGORY 1: GEO (Generative Engine Optimization)
                    {
                        id: 101,
                        catId: 'geo',
                        title: 'Winning the AI Snapshot (Zero-Click SEO)',
                        difficulty: 'Extreme',
                        impact: 'Critical',
                        content: `
                            <p>In 2026, ranking #1 is not enough. You must win the <strong>AI Overview (SGE)</strong> snapshot. AI models like Gemini and ChatGPT prioritize "Direct Answers" over long-form fluff.</p>
                            
                            <h3>1. The "Answer Block" Protocol</h3>
                            <p>Every service page (e.g., "Sell My House Fast [City]") must contain a specialized <strong>&lt;h2&gt;</strong> block designed for AI extraction.</p>
                            <div class="alert-box"><strong>Constraint:</strong> The answer must be 40-60 words long, objective, and placed immediately after the H2.</div>
                            
                            <h3>2. Implementation Script</h3>
                            <div class="copy-block">
                                <button class="copy-btn" onclick="alert('Copied!')">Copy Pattern</button>
                                &lt;h2&gt;How does selling a house for cash in [City] work?&lt;/h2&gt;
                                &lt;p&gt;Selling a house for cash in [City] involves three steps: requesting an offer, scheduling a 15-minute inspection, and closing at a local title company. This process bypasses listing fees, repairs, and lender appraisals, typically allowing homeowners to close in as little as 7 days.&lt;/p&gt;
                            </div>
                            
                            <h3>3. The "People Also Ask" (PAA) Clustering</h3>
                            <p>Do not just answer one question. Create a dedicated <strong>FAQ Section</strong> using the 5 most common PAA questions for that city. Code this using <code>FAQPage</code> Schema (see Tech SOP).</p>
                        `
                    },

                    // CATEGORY 2: LOCAL SIGNALS
                    {
                        id: 201,
                        catId: 'local',
                        title: 'Review Semantic Injection',
                        difficulty: 'Hard',
                        impact: 'High',
                        content: `
                            <p>Star ratings are table stakes. To rank for "Cash Home Buyer," Google looks for <strong>Review Justifications</strong>—bolded text in the map pack that matches the user's query.</p>
                            
                            <h3>The "Mad Libs" Review Request</h3>
                            <p>Do not ask for a "review." Send this specific script to clients after closing to force keyword injection naturally.</p>
                            
                            <div class="copy-block">
                                <button class="copy-btn" onclick="alert('Copied Script!')">Copy SMS</button>
                                "Hi [Name], glad we closed today! Could you do me a huge favor? When you leave a review, could you mention that we made you a **cash offer** and that we **bought your house as-is**? It helps other sellers find us. Thanks!"
                            </div>
                            
                            <h3>Why this works:</h3>
                            <p>When a user searches <em>"sell house as-is near me"</em>, Google scans reviews for the phrase "bought house as-is" and bolding it, jumping you above competitors with higher star counts but generic reviews.</p>
                        `
                    },
                    {
                        id: 202,
                        catId: 'local',
                        title: 'Neighborhood "Micro-Hubs" (Hyper-Local)',
                        difficulty: 'Medium',
                        impact: 'High',
                        content: `
                            <p>Zillow and Redfin dominate broad "City" keywords. You cannot beat them on Domain Authority. You win by going <strong>Hyper-Local</strong>.</p>
                            
                            <h3>Strategy: The Neighborhood Cluster</h3>
                            <p>Identify 5-10 specific neighborhoods or districts within the target city (e.g., instead of "Miami," target "Little Havana" or "Coconut Grove").</p>
                            
                            <h3>Content Structure for Micro-Hubs:</h3>
                            <ul>
                                <li><strong>H1:</strong> We Buy Houses in [Neighborhood], [City]</li>
                                <li><strong>Local Landmark Signal:</strong> "Located just 5 minutes from [Famous Park/School]..."</li>
                                <li><strong>Specific Pain Point:</strong> Mention specific housing stock issues (e.g., "We buy 1950s ranch homes with foundation issues common in [Neighborhood].")</li>
                            </ul>
                        `
                    },

                    // CATEGORY 3: CONTENT
                    {
                        id: 301,
                        catId: 'content',
                        title: 'The "Anti-Stock" Photo Policy',
                        difficulty: 'Easy',
                        impact: 'Medium',
                        content: `
                            <p>Google's Vision AI can detect stock photos. Using stock photos of "happy families" on a distress-oriented site signals <strong>Low Trust</strong> and hurts conversion.</p>
                            
                            <h3>Mandatory Asset List:</h3>
                            <ul>
                                <li><strong>The "Handshake" Shot:</strong> You shaking hands with a seller at the title company.</li>
                                <li><strong>The "Ugly" House:</strong> High-res photos of a house in true disrepair (hoarder houses, fire damage). This proves you actually buy "as-is" properties.</li>
                                <li><strong>The Contract:</strong> A photo of your actual Purchase Agreement (blurred personal info).</li>
                            </ul>
                            
                            <div class="alert-box"><strong>Protocol:</strong> All photos must be Geotagged with the target city's coordinates before upload.</div>
                        `
                    },

                    // CATEGORY 4: TECH & SCHEMA
                    {
                        id: 401,
                        catId: 'tech',
                        title: 'Advanced Schema for AI Context',
                        difficulty: 'Hard',
                        impact: 'Critical',
                        content: `
                            <p>Standard <code>LocalBusiness</code> schema is outdated. You must nest specific attributes to tell Google's AI exactly what you do.</p>
                            
                            <h3>JSON-LD Template (2026)</h3>
                            <p>Copy this into the <code>&lt;head&gt;</code>. Note the "areaServed" and "makesOffer" properties.</p>
                            
                            <div class="copy-block">
                                <button class="copy-btn" onclick="alert('Copied JSON-LD')">Copy Code</button>
                                &lt;script type="application/ld+json"&gt;
                                {
                                  "@context": "https://schema.org",
                                  "@type": "RealEstateAgent",
                                  "name": "[Business Name]",
                                  "image": "[Logo URL]",
                                  "priceRange": "$$$",
                                  "areaServed": [
                                    {"@type": "City", "name": "[City]"},
                                    {"@type": "Neighborhood", "name": "[Neighborhood 1]"}
                                  ],
                                  "makesOffer": {
                                    "@type": "Offer",
                                    "itemOffered": {
                                      "@type": "House",
                                      "name": "Residential Property"
                                    },
                                    "availability": "https://schema.org/InStock",
                                    "acceptedPaymentMethod": "Cash"
                                  }
                                }
                                &lt;/script&gt;
                            </div>
                        `
                    },

                    // CATEGORY 5: E-E-A-T & TRUST
                    {
                        id: 501,
                        catId: 'eeat',
                        title: 'Author Entity Optimization',
                        difficulty: 'Medium',
                        impact: 'Critical',
                        content: `
                            <p>In the YMYL (Your Money Your Life) sector, anonymous content is demoted. You must build an <strong>Author Entity</strong> that Google recognizes as an expert.</p>
                            
                            <h3>The "About" Page Protocol:</h3>
                            <ul>
                                <li><strong>Link to LinkedIn:</strong> The author's bio must link to a real LinkedIn profile.</li>
                                <li><strong>Accreditations:</strong> List specific REI memberships (e.g., "Member of [Local] REIA").</li>
                                <li><strong>Experience Signal:</strong> "Buying houses in [City] since [Year]."</li>
                            </ul>
                            
                            <h3>Digital Footprint:</h3>
                            <p>The author should have at least 2 guest posts on <strong>other</strong> real estate sites (BiggerPockets, local news) pointing back to their bio page to validate their identity.</p>
                        `
                    }
                ]
            }
        };

        const GSC_API = 'api/gsc-sites.php';

        /** Build a full client object from a GSC site entry (so existing UI/tabs work). */
        const gscSiteToClient = (site, index) => {
            const siteUrl = site.siteUrl || '';
            const name = siteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '') || 'Property';
            const history = [];
            for (let d = 0; d < 90; d++) {
                history.push({ day: `Day ${d+1}`, imp: 0, leads: 0 });
            }
            return {
                id: 1000 + index,
                name: name,
                url: siteUrl,
                specialist: '—',
                location: '',
                startDate: '',
                permissionLevel: site.permissionLevel || '',
                status: { onPage: 'Pending', offPage: 'Pending', tech: 'Pending', disavow: 'Pending', benchmark: 'Pending' },
                kpi: { impressions: 0, ctr: '0', leads: 0, trend30: 0, history },
                tasks: [],
                keywords: [{ query: '—', volume: 0, pos: 0, ctr: 0, gap: [] }],
                ai: { da: 0, competitorDA: 0, backlinkGap: 0, contentScore: 0, entities: [] },
                assets: { schema: [], citations: [], backlinks: { distribution: [0,0,0], targets: [] } },
                logs: [],
                notes: ''
            };
        };

        const fetchGscSites = async () => {
            try {
                const r = await fetch(GSC_API);
                const data = await r.json();
                return data;
            } catch (e) {
                return { error: 'api_error', message: e.message };
            }
        };

        const loadMockData = () => {
            const names = ["Sky Mount Buys Houses", "Ana Home Buyers", "Byron Buys Homes", "Charm City Builders", "Crof Maryland", "HK Buy Houses", "Vegas Cash Offers", "Miami Quick Sale"];
            const specialists = ["Gene", "Dylan", "Jaeson", "Zaldy"];
            const locations = ["Lawton, OK", "Anaheim, CA", "Minneapolis, MN", "Baltimore, MD", "Crofton, MD", "Hong Kong", "Las Vegas, NV", "Miami, FL"];
            
            DB.clients = names.map((name, i) => {
                const spec = specialists[i % 4];
                const hasIssue = Math.random() < 0.15;
                
                // History Generator (90 Days)
                const history = [];
                let imp = 1000 + Math.random() * 5000;
                let leads = 5 + Math.random() * 20;
                for (let d = 0; d < 90; d++) {
                    imp += (Math.random() - 0.4) * 200; 
                    leads += (Math.random() - 0.45) * 2;
                    history.push({ day: `Day ${d+1}`, imp: Math.max(0, imp), leads: Math.max(0, leads) });
                }

                // V2: Deep Data - Keywords & Entities
                const entities = ["Cash Offer", "As Is", "Fast Closing", "No Fees", "Inherited Property", "Avoid Foreclosure", "Fair Price", "Real Estate Agent", "Repair Costs", "Probate"];
                const keywords = [
                    { query: `sell house fast ${locations[i]}`, volume: 1200, pos: (i*2)+3.5, ctr: 1.2, gap: ["Semantic Entity: 'Cash Offer'", "LSI: 'Avoid Foreclosure'"] },
                    { query: `we buy houses ${locations[i]}`, volume: 880, pos: (i)+8.2, ctr: 0.8, gap: ["Word Count: +200 words needed"] }
                ];

                // V2: Deep Data - Kanban Tasks
                const tasks = [
                    { id: 101+i, title: "Monthly Tech Audit", type: "Recurring", status: "todo", priority: "high", timeSpent: 0, avgTime: 45, active: false, user: "JD" },
                    { id: 102+i, title: "Optimize H1 Headers", type: "One-Off", status: "inprogress", priority: "med", timeSpent: 12, avgTime: 15, active: true, user: spec.substring(0,2) },
                    { id: 103+i, title: "Fix Broken Backlinks", type: "CSM Request", status: "done", priority: "high", timeSpent: 25, avgTime: 20, active: false, user: "JD" }
                ];

                return {
                    id: 1000 + i,
                    name: name,
                    url: name.toLowerCase().replace(/\s/g, "") + ".com",
                    specialist: spec,
                    location: locations[i],
                    startDate: "2024-06-01",
                    
                    // V8 High Level Status
                    status: {
                        onPage: Math.random() > 0.6 ? 'Done' : 'Pending',
                        offPage: Math.random() > 0.7 ? 'Done' : 'Pending',
                        tech: hasIssue ? 'Issue' : (Math.random() > 0.5 ? 'Done' : 'Pending'),
                        disavow: Math.random() > 0.8 ? 'Done' : 'Pending',
                        benchmark: 'Done'
                    },
                    
                    // V2 & V8 Metrics
                    kpi: {
                        impressions: Math.round(history[89].imp),
                        ctr: (1.5 + Math.random() * 3).toFixed(2),
                        leads: Math.round(history[89].leads),
                        trend30: Math.round((Math.random() - 0.3) * 20),
                        history: history
                    },

                    // V2 Deep Data
                    tasks: tasks,
                    keywords: keywords,
                    ai: { 
                        da: 32 + Math.floor(Math.random()*20), 
                        competitorDA: 45 + Math.floor(Math.random()*10), 
                        backlinkGap: Math.floor(Math.random() * 8) + 2, 
                        contentScore: 45, 
                        entities: entities 
                    },
                    assets: {
                        schema: [{ type: "LocalBusiness", status: "valid", details: "NAP matches GMB" }, {type: "FAQPage", status: "warning", details: "Missing mainEntity"}],
                        citations: [{ name: "Google Business Profile", status: "live", nap: "Perfect" }, { name: "Yelp", status: "missing", nap: "N/A" }],
                        backlinks: { distribution: [60, 30, 10], targets: [{ url: "realtor.com/blog", da: 89, status: "Outreach Sent" }] }
                    },
                    logs: [
                        { type: "system", user: "System", action: "Campaign Created", time: "2024-06-01 09:00", details: "Initial setup complete." },
                        { type: "alert", user: "System", action: "Keyword Movement", time: "2024-06-15 14:20", details: "Entered Top 10 for 'Sell Fast'" }
                    ],
                    notes: hasIssue ? "Critical: Deindexed pages detected via GSC." : ""
                };
            });
        };

        // --- UI RENDERER ---
        const UI = {
            
            // 1. DASHBOARD VIEW
            renderDashboard: (filterIssue = false) => {
                const isManager = DB.currentUser === 'Gene';
                let clients = isManager ? DB.clients : DB.clients.filter(c => c.specialist === DB.currentUser);
                if(filterIssue) clients = clients.filter(c => Object.values(c.status).includes('Issue') || c.notes.length > 0);

                document.getElementById('page-title').innerText = filterIssue ? "Issues Watchlist" : "Master Dashboard";
                document.getElementById('total-clients').innerText = clients.length;
                
                const issueCount = DB.clients.filter(c => Object.values(c.status).includes('Issue') || c.notes.length > 0).length;
                const badge = document.getElementById('issue-badge');
                if(issueCount > 0) { badge.classList.remove('hidden'); badge.innerText = issueCount; }

                const headers = DB.viewMode === 'ops' 
                    ? `<th class="text-center">On-Page</th><th class="text-center">Off-Page</th><th class="text-center">Tech</th><th class="text-center">Disavow</th><th class="text-center">Benchmark</th>`
                    : `<th class="text-right">Impressions (30d)</th><th class="text-right">CTR</th><th class="text-right">SEO Leads</th><th class="text-right">Trend</th>`;

                const gscConnectBanner = DB.gscAuthUrl ? `
                <div class="mb-6 bg-sky-50 border border-sky-200 rounded-xl p-6 flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-white border border-sky-200 flex items-center justify-center"><i class="fa-brands fa-google text-sky-600 text-xl"></i></div>
                        <div>
                            <h3 class="font-bold text-slate-800">Connect Google Search Console</h3>
                            <p class="text-sm text-slate-600">Load your verified sites and properties into the Master List.</p>
                        </div>
                    </div>
                    <a href="${DB.gscAuthUrl}" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg text-sm transition shadow-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-link"></i> Connect GSC
                    </a>
                </div>` : '';
                const gscSetupBanner = DB.gscSetupMessage ? `
                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-6 flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-white border border-amber-200 flex items-center justify-center"><i class="fa-solid fa-gear text-amber-600 text-xl"></i></div>
                        <div>
                            <h3 class="font-bold text-slate-800">Google Search Console setup</h3>
                            <p class="text-sm text-slate-600">${DB.gscSetupMessage}</p>
                            <p class="text-xs text-slate-500 mt-1">See <strong>README-GSC.md</strong> in the seo folder for step-by-step instructions.</p>
                        </div>
                    </div>
                </div>` : '';
                const gscBanner = gscConnectBanner || gscSetupBanner;

                return `
                <div class="fade-in">
                ${gscBanner}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col h-full overflow-hidden">
                    <div class="overflow-auto flex-1">
                        <table class="w-full text-left master-table">
                            <thead>
                                <tr>
                                    <th class="pl-8 w-1/4">Client Name / URL</th>
                                    <th>Specialist</th>
                                    ${headers}
                                    <th class="text-right pr-8">Action</th>
                                </tr>
                            </thead>
                            <tbody id="client-list">
                                ${clients.map(c => UI.renderRow(c)).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>`;
            },

            renderRow: (c) => {
                const hasIssue = Object.values(c.status).includes('Issue') || c.notes.length > 0;
                
                let columns = '';
                if(DB.viewMode === 'ops') {
                    columns = `
                    <td class="text-center">${UI.pill(c.id, 'onPage', c.status.onPage)}</td>
                    <td class="text-center">${UI.pill(c.id, 'offPage', c.status.offPage)}</td>
                    <td class="text-center">${UI.pill(c.id, 'tech', c.status.tech)}</td>
                    <td class="text-center">${UI.pill(c.id, 'disavow', c.status.disavow)}</td>
                    <td class="text-center">${UI.pill(c.id, 'benchmark', c.status.benchmark)}</td>`;
                } else {
                    const trendIcon = c.kpi.trend30 >= 0 ? '<i class="fa-solid fa-arrow-trend-up"></i>' : '<i class="fa-solid fa-arrow-trend-down"></i>';
                    const trendColor = c.kpi.trend30 >= 0 ? 'text-emerald-600' : 'text-red-600';
                    columns = `
                    <td class="text-right font-mono text-xs font-bold text-slate-700">${c.kpi.impressions.toLocaleString()}</td>
                    <td class="text-right font-mono text-xs text-slate-500">${c.kpi.ctr}%</td>
                    <td class="text-right font-mono text-xs font-bold text-indigo-600">${c.kpi.leads}</td>
                    <td class="text-right font-mono text-xs ${trendColor} font-bold">${trendIcon} ${Math.abs(c.kpi.trend30)}%</td>`;
                }

                return `
                <tr class="${hasIssue ? 'bg-red-50 hover:bg-red-100 border-l-4 border-l-red-500' : 'hover:bg-slate-50 transition'} group cursor-pointer" onclick="App.openClient(${c.id})">
                    <td class="pl-8 py-4">
                        <div class="font-bold text-slate-800 text-sm group-hover:text-sky-600 transition-colors">${c.name}</div>
                        <div class="text-[10px] ${hasIssue ? 'text-red-700 font-bold' : 'text-gray-400 font-mono'}">
                            ${hasIssue ? `<i class="fa-solid fa-circle-exclamation mr-1"></i> ${c.notes}` : c.url}
                        </div>
                    </td>
                    <td><span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-[10px] font-bold border border-slate-200 uppercase">${c.specialist}</span></td>
                    ${columns}
                    <td class="text-right pr-8">
                        <button class="text-slate-300 hover:text-sky-600 transition-colors"><i class="fa-solid fa-chevron-right"></i></button>
                    </td>
                </tr>`;
            },

            pill: (id, type, status) => {
                const map = { 'Done': 'pill-done', 'Pending': 'pill-pending', 'Issue': 'pill-issue' };
                const label = status === 'Pending' ? '---' : status;
                return `<span onclick="event.stopPropagation(); App.toggleStatus(${id}, '${type}')" class="pill ${map[status]}">${label}</span>`;
            },

            // 2. CLIENT COCKPIT
            renderCockpit: (client) => {
                return `
                <div class="flex flex-col h-full gap-6 fade-in">
                    <div class="flex border-b border-gray-200 bg-white rounded-t-lg px-4 pt-2 shadow-sm">
                        <button onclick="App.switchTab('performance', ${client.id})" id="tab-performance" class="tab-btn active"><i class="fa-solid fa-chart-line mr-2"></i> Performance</button>
                        <button onclick="App.switchTab('workflow', ${client.id})" id="tab-workflow" class="tab-btn"><i class="fa-solid fa-list-check mr-2"></i> Workflow</button>
                        <button onclick="App.switchTab('strategy', ${client.id})" id="tab-strategy" class="tab-btn"><i class="fa-solid fa-chess mr-2"></i> AI Strategy</button>
                        <button onclick="App.switchTab('content', ${client.id})" id="tab-content" class="tab-btn"><i class="fa-solid fa-pen-nib mr-2"></i> Content</button>
                        <button onclick="App.switchTab('tech', ${client.id})" id="tab-tech" class="tab-btn"><i class="fa-solid fa-server mr-2"></i> Tech & Assets</button>
                        <button onclick="App.switchTab('logs', ${client.id})" id="tab-logs" class="tab-btn"><i class="fa-solid fa-clock-rotate-left mr-2"></i> Ledger</button>
                    </div>
                    <div id="tab-viewport" class="bg-white rounded-b-lg border border-gray-200 shadow-sm p-8 overflow-y-auto flex-1">
                        </div>
                </div>`;
            },
            
            // 3. SOP LIBRARY
            renderSOPs: () => {
                document.getElementById('page-title').innerText = "Standard Operating Procedures (REI)";
                const defaultSOP = DB.sopLibrary.articles[0];
                return `
                <div class="flex h-full gap-6 fade-in">
                    <div class="w-64 flex-shrink-0 bg-white border border-gray-200 rounded-lg p-4 h-full overflow-y-auto">
                        <div class="mb-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Library Categories</div>
                        <div class="space-y-6">
                            ${DB.sopLibrary.categories.map(cat => `
                                <div>
                                    <div class="font-bold text-slate-800 text-xs mb-2 flex items-center gap-2"><i class="fa-solid fa-folder text-sky-500"></i> ${cat.name}</div>
                                    <div class="space-y-1 ml-4 border-l border-slate-200 pl-2">
                                        ${DB.sopLibrary.articles.filter(a => a.catId === cat.id).map(a => `
                                            <div onclick="App.loadSOP(${a.id})" id="sop-nav-${a.id}" class="sop-nav-item ${a.id === defaultSOP.id ? 'active' : ''}">${a.title}</div>
                                        `).join('')}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="flex-1 bg-white border border-gray-200 rounded-lg shadow-sm p-8 overflow-y-auto h-full relative">
                        <div id="sop-viewer">${UI.renderSOPDetail(defaultSOP)}</div>
                    </div>
                </div>`;
            },
            
            renderSOPDetail: (sop) => {
                const diffColor = sop.difficulty === 'Essential' ? 'bg-indigo-100 text-indigo-700' : (sop.difficulty === 'Hard' ? 'bg-red-100 text-red-700' : (sop.difficulty === 'Extreme' ? 'bg-slate-800 text-white' : 'bg-green-100 text-green-700'));
                const impactColor = sop.impact === 'High' ? 'bg-emerald-100 text-emerald-700' : (sop.impact === 'Critical' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700');
                
                return `
                <div class="max-w-3xl mx-auto sop-content fade-in">
                    <div class="flex gap-2 mb-4">
                        <span class="text-xs font-bold px-2 py-1 rounded ${diffColor} border border-transparent">${sop.difficulty}</span>
                        <span class="text-xs font-bold px-2 py-1 rounded ${impactColor} border border-transparent">Impact: ${sop.impact}</span>
                    </div>
                    <h1>${sop.title}</h1>
                    <div class="h-px bg-slate-200 w-full mb-8"></div>
                    <div>${sop.content}</div>
                </div>`;
            },

            // 4. ROBUST OUTREACH GEN (NEW FEATURE)
            renderOutreach: () => {
                document.getElementById('page-title').innerText = "Outreach Generator";
                return `
                <div class="grid grid-cols-12 gap-8 h-full fade-in">
                    <div class="col-span-5 flex flex-col gap-6">
                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass text-sky-500"></i> Google Operator Engine
                            </h3>
                            <p class="text-xs text-slate-500 mb-4">Generate advanced search strings to find guest post opportunities in the REI niche.</p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Target Niche</label>
                                    <select id="op-niche" class="w-full border border-slate-200 rounded p-2 text-xs font-bold text-slate-700 outline-none focus:border-sky-500">
                                        <option value="real estate">Real Estate Blogs</option>
                                        <option value="home improvement">Home Improvement / DIY</option>
                                        <option value="personal finance">Personal Finance / Debt</option>
                                        <option value="legal">Probate / Legal</option>
                                        <option value="news">Local News / Media</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Footprint Type</label>
                                    <select id="op-footprint" class="w-full border border-slate-200 rounded p-2 text-xs font-bold text-slate-700 outline-none focus:border-sky-500">
                                        <option value='"write for us"'>Standard ("Write for us")</option>
                                        <option value='"guest post"'>Guest Post Mention</option>
                                        <option value='inurl:blog "real estate"'>Niche Blog Search</option>
                                        <option value='"sponsored post"'>Sponsored / Paid</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Local Modifier (Optional)</label>
                                    <input type="text" id="op-city" placeholder="e.g. Miami" class="w-full border border-slate-200 rounded p-2 text-xs outline-none focus:border-sky-500">
                                </div>
                                
                                <button onclick="App.runOperator()" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 rounded text-xs transition shadow-sm">
                                    <i class="fa-brands fa-google mr-2"></i> Launch Search
                                </button>
                            </div>
                        </div>

                        <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-lg relative overflow-hidden">
                            <div class="relative z-10">
                                <h3 class="font-bold mb-2">Outreach KPI</h3>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-3xl font-black">12</div>
                                        <div class="text-[10px] text-indigo-300 uppercase">Pitches Sent (This Week)</div>
                                    </div>
                                    <div class="text-right">
                                         <div class="text-xl font-bold text-emerald-400">18%</div>
                                         <div class="text-[10px] text-indigo-300 uppercase">Reply Rate</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-7 flex flex-col h-full">
                        <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm h-full flex flex-col">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="font-bold text-slate-800 text-lg">Pitch Generator</h3>
                                <div class="flex gap-2">
                                     <button onclick="App.setTemplate('guest')" class="px-3 py-1 rounded border border-slate-200 text-xs font-bold hover:bg-slate-50 text-slate-600 transition">Guest Post</button>
                                     <button onclick="App.setTemplate('link')" class="px-3 py-1 rounded border border-slate-200 text-xs font-bold hover:bg-slate-50 text-slate-600 transition">Link Fix</button>
                                     <button onclick="App.setTemplate('pr')" class="px-3 py-1 rounded border border-slate-200 text-xs font-bold hover:bg-slate-50 text-slate-600 transition">Local PR</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <input id="gen-name" type="text" placeholder="Target Name (e.g. Mike)" class="border border-slate-200 rounded p-3 text-xs outline-none focus:border-sky-500" oninput="App.updateEmailPreview()">
                                <input id="gen-site" type="text" placeholder="Target Site Name" class="border border-slate-200 rounded p-3 text-xs outline-none focus:border-sky-500" oninput="App.updateEmailPreview()">
                                <input id="gen-topic" type="text" placeholder="Proposed Topic / City" class="border border-slate-200 rounded p-3 text-xs outline-none focus:border-sky-500" oninput="App.updateEmailPreview()">
                                <input id="gen-myname" type="text" value="Gene" class="border border-slate-200 rounded p-3 text-xs outline-none focus:border-sky-500" oninput="App.updateEmailPreview()">
                            </div>

                            <div class="flex-1 bg-slate-50 border border-slate-200 rounded-lg p-6 relative group">
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <button onclick="App.copyEmail()" class="bg-white border border-slate-300 shadow-sm px-3 py-1 rounded text-xs font-bold text-slate-600 hover:text-sky-600">Copy to Clipboard</button>
                                </div>
                                <div id="email-preview" class="text-sm text-slate-700 leading-relaxed font-serif whitespace-pre-wrap">Select a template above to start...</div>
                            </div>
                        </div>
                    </div>
                </div>`;
            },

            // --- TAB CONTENT HANDLERS ---
            getTabContent: (tab, client) => {
                // 1. PERFORMANCE TAB
                if(tab === 'performance') {
                    const kw = client.keywords[0];
                    const potentialTraffic = Math.round(kw.volume * 0.32);
                    return `
                    <div class="space-y-8">
                        <div class="grid grid-cols-4 gap-4">
                            <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2">Impressions</p>
                                <h3 class="text-3xl font-black text-slate-800">${client.kpi.impressions.toLocaleString()}</h3>
                                <p class="text-xs text-emerald-600 font-bold mt-2"><i class="fa-solid fa-arrow-up"></i> 12% vs last mo</p>
                            </div>
                            <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2">SEO Leads</p>
                                <h3 class="text-3xl font-black text-indigo-600">${client.kpi.leads}</h3>
                                <p class="text-xs text-emerald-600 font-bold mt-2"><i class="fa-solid fa-fire"></i> High Intent</p>
                            </div>
                            <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2">Avg CTR</p>
                                <h3 class="text-3xl font-black text-slate-800">${client.kpi.ctr}%</h3>
                            </div>
                            <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2">Health Score</p>
                                <h3 class="text-3xl font-black text-emerald-600">98%</h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="p-6 border border-slate-200 rounded-xl shadow-sm"><h3 class="font-bold text-slate-700 mb-6 text-sm">Traffic Trend (90 Days)</h3><div class="h-64 w-full"><canvas id="chartImpressions"></canvas></div></div>
                            <div class="p-6 border border-slate-200 rounded-xl shadow-sm"><h3 class="font-bold text-slate-700 mb-6 text-sm">Conversion Trend</h3><div class="h-64 w-full"><canvas id="chartLeads"></canvas></div></div>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-lightbulb text-yellow-500"></i> High-Impact Opportunities</h3>
                            <div class="overflow-hidden border border-slate-200 rounded-lg shadow-sm">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-gray-50 font-bold text-gray-500 uppercase"><tr><th class="p-4">Query</th><th class="p-4 text-right">Vol</th><th class="p-4 text-right">Pos</th><th class="p-4 text-right">CTR</th><th class="p-4">Strategy</th></tr></thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <tr><td class="p-4 font-mono font-bold text-slate-700">${kw.query}</td><td class="p-4 text-right">${kw.volume}</td><td class="p-4 text-right font-bold text-indigo-600">#${kw.pos}</td><td class="p-4 text-right text-red-500 font-bold">${kw.ctr}%</td><td class="p-4"><button class="text-sky-600 font-bold hover:underline" onclick="App.switchTab('content', ${client.id})">Optimize Content (+${potentialTraffic} Visits)</button></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>`;
                }

                // 2. WORKFLOW TAB
                if(tab === 'workflow') {
                    const todo = client.tasks.filter(t => t.status === 'todo');
                    const inprog = client.tasks.filter(t => t.status === 'inprogress');
                    const done = client.tasks.filter(t => t.status === 'done');
                    return `
                    <div class="grid grid-cols-12 gap-6 h-full">
                        <div class="col-span-9 grid grid-cols-3 gap-6 h-full">
                            <div class="kanban-col"><div class="kanban-header"><span>To Do (${todo.length})</span><i class="fa-solid fa-plus cursor-pointer hover:text-indigo-600"></i></div>${todo.map(t => UI.renderTaskCard(t, client.id)).join('')}</div>
                            <div class="kanban-col border-t-4 border-t-sky-500"><div class="kanban-header"><span class="text-sky-700">In Progress (${inprog.length})</span><i class="fa-solid fa-spinner fa-spin text-sky-400"></i></div>${inprog.map(t => UI.renderTaskCard(t, client.id)).join('')}</div>
                            <div class="kanban-col"><div class="kanban-header"><span>Completed</span></div>${done.map(t => UI.renderTaskCard(t, client.id)).join('')}</div>
                        </div>
                        <div class="col-span-3 space-y-6">
                            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm"><h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2"><i class="fa-solid fa-robot text-purple-500"></i> Process Intel</h3><div class="space-y-4 text-xs text-slate-600"><div class="flex justify-between border-b border-slate-100 pb-2"><span>Your Avg Time:</span><span class="font-bold text-slate-800">32m / task</span></div><div class="flex justify-between border-b border-slate-100 pb-2"><span>Team Benchmark:</span><span class="font-bold text-slate-800">35m / task</span></div><p class="text-emerald-600 font-bold flex items-center gap-2 mt-2"><i class="fa-solid fa-circle-check"></i> 8% Faster</p></div></div>
                            <div class="bg-yellow-50 p-5 rounded-xl border border-yellow-200 shadow-sm"><h3 class="font-bold text-yellow-800 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-bell"></i> CSM Alerts</h3><p class="text-xs text-yellow-700 mb-4 leading-relaxed">Tasks injected by Client Success require immediate priority.</p><button class="w-full bg-white border border-yellow-300 text-yellow-700 text-xs font-bold py-2.5 rounded-lg hover:bg-yellow-100 transition">View Escalations</button></div>
                        </div>
                    </div>`;
                }

                // 3. AI STRATEGY
                if(tab === 'strategy') {
                    const authorityGap = client.ai.competitorDA - client.ai.da;
                    const winProb = Math.max(10, 100 - (authorityGap * 5));
                    return `
                    <div class="grid grid-cols-12 gap-8 h-full">
                        <div class="col-span-8 space-y-6">
                            <div class="strategy-banner flex justify-between items-center"><div class="relative z-10"><div class="flex items-center gap-2 mb-2"><div class="w-2 h-2 rounded-full bg-white animate-pulse"></div><h3 class="font-bold text-xs uppercase tracking-widest text-indigo-200">AI Rank Equation</h3></div><h4 class="font-bold text-2xl mb-1 text-white">Win Probability: ${winProb}%</h4><p class="text-sm font-medium text-indigo-100 opacity-90">Based on Authority Gap and Content Velocity vs Top 3.</p></div><div class="relative z-10 text-right"><div class="text-3xl font-black text-white">${authorityGap > 0 ? '-' + authorityGap : '+Leader'}</div><div class="text-xs font-bold text-indigo-200 uppercase">DA Gap</div></div></div>
                            <div class="border border-slate-200 rounded-xl p-6 bg-white shadow-sm"><div class="flex justify-between items-center mb-4"><h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-dna text-pink-500 mr-2"></i> Semantic Entity Gap</h3><span class="text-xs text-slate-400">Entities found in Top 3 but missing from your page</span></div><div class="space-y-2">${client.keywords[0].gap.map(g => `<div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded hover:border-pink-300 transition-colors"><div class="flex items-center gap-3"><i class="fa-solid fa-triangle-exclamation text-orange-400 text-xs"></i><span class="text-sm font-bold text-slate-700">${g}</span></div><button class="text-[10px] font-bold bg-white border border-slate-200 px-3 py-1 rounded text-slate-600 hover:text-pink-600 hover:border-pink-300">Auto-Inject</button></div>`).join('')}</div></div>
                        </div>
                        <div class="col-span-4 bg-slate-900 rounded-xl p-6 text-white relative overflow-hidden"><h3 class="font-bold text-lg mb-6">Execution Protocol</h3><div class="space-y-6 relative z-10"><div class="flex gap-4 items-start"><div class="flex flex-col items-center"><div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold">1</div><div class="w-0.5 h-full bg-slate-700 my-1"></div></div><div><h4 class="font-bold text-sm">Close Authority Gap</h4><p class="text-xs text-slate-400 mb-2">Acquire ${client.ai.backlinkGap} links from DA ${client.ai.competitorDA}+ sites.</p></div></div><div class="flex gap-4 items-start"><div class="flex flex-col items-center"><div class="w-6 h-6 rounded-full bg-pink-500 flex items-center justify-center text-xs font-bold">2</div></div><div><h4 class="font-bold text-sm">Semantic Injection</h4><p class="text-xs text-slate-400 mb-2">Update landing page with missing NLP entities.</p></div></div></div></div>
                    </div>`;
                }

                // 4. CONTENT TAB
                if(tab === 'content') {
                    return `
                    <div class="grid grid-cols-12 gap-8 h-full">
                        <div class="col-span-8 flex flex-col h-full"><div class="flex justify-between items-center mb-4"><h3 class="font-bold text-gray-800 text-lg">Live Editor</h3><div class="flex gap-2"><button class="text-xs bg-white border border-slate-300 px-3 py-1.5 rounded hover:bg-slate-50 font-bold"><i class="fa-solid fa-floppy-disk"></i> Save</button><button class="text-xs bg-pink-600 text-white px-3 py-1.5 rounded hover:bg-pink-700 font-bold">Publish to WP</button></div></div><div class="editor-container"><div class="editor-toolbar"><i class="fa-solid fa-bold hover:text-slate-800 cursor-pointer"></i><i class="fa-solid fa-italic hover:text-slate-800 cursor-pointer"></i><div class="h-4 w-px bg-slate-300 mx-2"></div><i class="fa-solid fa-heading hover:text-slate-800 cursor-pointer"></i></div><div id="document-editor" class="editor-content" contenteditable="true" oninput="App.checkContent(${client.id})"><h1 class="text-2xl font-bold mb-4">Sell Your House Fast in ${client.location}</h1><p>We buy houses in ${client.location} for cash. If you need to sell your house fast, we can give you a fair price.</p><p><br></p><p>Start typing to hit the entity targets...</p></div></div></div>
                        <div class="col-span-4 flex flex-col h-full"><div class="bg-white border border-slate-200 rounded-xl p-6 h-full flex flex-col shadow-sm"><div class="flex items-center justify-between mb-6"><h3 class="font-bold text-slate-800">Entity Decoder</h3><div id="content-score-circle" class="w-10 h-10 rounded-full border-4 border-slate-200 flex items-center justify-center font-bold text-slate-400 text-sm">0</div></div><div class="mb-4"><div class="text-xs font-bold text-slate-400 uppercase mb-2">NLP Targets</div><div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden"><div id="progress-bar" class="bg-pink-500 h-full w-0 transition-all duration-500"></div></div></div><div id="entity-list" class="flex-1 overflow-y-auto space-y-2 pr-1">${client.ai.entities.map(e => `<div id="entity-${e.replace(/\s/g, '')}" class="entity-item"><span class="text-slate-600 font-medium">${e}</span><i class="fa-regular fa-circle text-slate-300"></i></div>`).join('')}</div></div></div>
                    </div>`;
                }
                
                // 5. TECH & ASSETS
                if(tab === 'tech') {
                    return `
                    <div class="grid grid-cols-12 gap-6 h-full">
                        <div class="col-span-4 flex flex-col"><div class="bg-white border border-slate-200 rounded-xl p-5 h-full flex flex-col shadow-sm"><h3 class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-code text-blue-500 mr-2"></i> Schema Architect</h3><div class="space-y-3 flex-1 overflow-y-auto">${client.assets.schema.map(s => `<div class="p-3 bg-slate-50 border border-slate-100 rounded flex justify-between items-center"><div><span class="text-xs font-bold text-slate-700 block">${s.type}</span><span class="text-[10px] text-slate-400">${s.details}</span></div><span class="w-2 h-2 rounded-full ${s.status === 'valid' ? 'bg-green-500' : 'bg-yellow-500'}"></span></div>`).join('')}</div></div></div>
                        <div class="col-span-4 flex flex-col"><div class="bg-white border border-slate-200 rounded-xl p-5 h-full flex flex-col shadow-sm"><h3 class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-map-location-dot text-emerald-500 mr-2"></i> Citation Matrix</h3><div class="space-y-3 flex-1 overflow-y-auto">${client.assets.citations.map(c => `<div class="p-3 bg-slate-50 border border-slate-100 rounded flex justify-between items-center"><div><span class="text-xs font-bold text-slate-700 block">${c.name}</span><span class="text-[10px] text-slate-400">NAP: ${c.nap}</span></div><span class="text-[9px] font-bold uppercase ${c.status === 'live' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'} px-2 py-0.5 rounded">${c.status}</span></div>`).join('')}</div></div></div>
                        <div class="col-span-4 flex flex-col"><div class="bg-white border border-slate-200 rounded-xl p-5 h-full flex flex-col shadow-sm"><h3 class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-link text-indigo-500 mr-2"></i> Backlink Vault</h3><div class="h-32 w-full mb-4"><canvas id="chartAnchors"></canvas></div><div class="text-[10px] font-bold text-slate-400 uppercase mb-2">Recent Targets</div><div class="space-y-2">${client.assets.backlinks.targets.map(t => `<div class="flex justify-between items-center text-xs p-2 bg-slate-50 rounded"><span>${t.url} <span class="text-[9px] text-slate-400 bg-slate-200 px-1 rounded ml-1">DA ${t.da}</span></span><span class="font-bold text-indigo-600">${t.status}</span></div>`).join('')}</div></div></div>
                    </div>`;
                }

                // 6. LEDGER
                if(tab === 'logs') {
                    const sortedLogs = [...client.logs].sort((a,b) => new Date(b.time) - new Date(a.time));
                    return `
                    <div class="grid grid-cols-12 gap-8 h-full">
                        <div class="col-span-7 bg-white">
                            <div class="flex justify-between items-center mb-6"><h3 class="font-bold text-slate-800 text-lg">Immutable Change Ledger</h3><button class="text-[10px] bg-slate-100 px-3 py-1 rounded hover:bg-slate-200 font-bold text-slate-600">Export CSV</button></div>
                            <div class="space-y-0 pl-2 border-l border-slate-200 ml-2">${sortedLogs.map(log => { const dotColor = log.type === 'user' ? 'bg-blue-500' : (log.type === 'alert' ? 'bg-yellow-500' : 'bg-slate-400'); const icon = log.type === 'user' ? 'fa-user' : (log.type === 'alert' ? 'fa-bell' : 'fa-server'); return `<div class="relative pl-6 pb-6"><div class="absolute -left-[9px] top-1 w-5 h-5 rounded-full ${dotColor} flex items-center justify-center text-white text-[8px] border-2 border-white"><i class="fa-solid ${icon}"></i></div><div class="flex justify-between items-start"><div><span class="text-xs font-bold text-slate-700">${log.action}</span><span class="text-[10px] text-slate-400 ml-2">by ${log.user}</span></div><span class="text-[10px] font-mono text-slate-400">${log.time}</span></div><p class="text-xs text-slate-600 mt-1 bg-slate-50 p-2 rounded border border-slate-100">${log.details}</p></div>`; }).join('')}</div>
                        </div>
                        <div class="col-span-5 bg-slate-50 p-6 rounded-lg border border-slate-200 flex flex-col"><h3 class="font-bold text-slate-800 text-lg mb-4">Strategic Notes</h3><div class="flex-1 mb-4 overflow-y-auto max-h-[400px] space-y-3"><div class="bg-white p-3 rounded border border-slate-200 shadow-sm"><p class="text-xs text-slate-600">Client called. Wants to pivot focus to "Probate" keywords.</p><div class="mt-2 text-[10px] text-slate-400 flex justify-between"><span>Gene</span> <span>2 days ago</span></div></div></div><div class="mt-auto"><textarea id="new-note" class="w-full border border-slate-300 rounded p-3 text-xs focus:ring-2 focus:ring-sky-500 outline-none" rows="3" placeholder="Add a strategic note..."></textarea><button onclick="App.addNote(${client.id})" class="w-full mt-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-2 rounded transition">Log Note</button></div></div>
                    </div>`;
                }
            },

            // --- KANBAN CARD RENDERER ---
            renderTaskCard: (task, clientId) => {
                let tagClass = task.type === 'Recurring' ? 'tag-recurring' : (task.type === 'CSM Request' ? 'tag-csm' : 'tag-oneoff');
                let timerClass = task.active ? 'timer-active' : 'text-slate-500';
                
                return `
                <div class="kanban-card group">
                    <div class="flex justify-between items-start mb-2">
                        <span class="${tagClass}">${task.type}</span>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-xs text-slate-400 hover:text-indigo-600 cursor-pointer" onclick="App.moveTask(${clientId}, ${task.id}, 'next')"></i>
                        </div>
                    </div>
                    <p class="font-bold text-slate-700 text-sm mb-3 leading-snug">${task.title}</p>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-3">
                        <div class="flex items-center gap-2 cursor-pointer ${timerClass} text-xs font-mono" onclick="App.toggleTimer(${clientId}, ${task.id})">
                            <i class="fa-solid ${task.active ? 'fa-pause' : 'fa-play'}"></i>
                            <span>${task.timeSpent}m / ${task.avgTime}m</span>
                        </div>
                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-bold border border-white shadow-sm">${task.user}</div>
                    </div>
                </div>`;
            }
        };

        // --- CONTROLLER ---
        const App = {
            currentTemplate: 'guest',

            init: async () => {
                const params = new URLSearchParams(window.location.search);
                const gscParam = params.get('gsc');
                if (gscParam === 'connected') {
                    history.replaceState({}, '', window.location.pathname);
                }
                const gsc = await fetchGscSites();
                DB.gscAuthUrl = null;
                DB.gscSetupMessage = null;
                if (gsc.sites && gsc.sites.length > 0) {
                    DB.clients = gsc.sites.map((s, i) => gscSiteToClient(s, i));
                } else if (gsc.auth_url) {
                    DB.gscAuthUrl = gsc.auth_url;
                    DB.clients = [];
                } else {
                    if (gsc.error === 'not_configured' && gsc.message) {
                        DB.gscSetupMessage = gsc.message;
                        DB.clients = [];
                    } else {
                        loadMockData();
                    }
                }
                App.router('dashboard');
                if (gscParam === 'connected') {
                    document.getElementById('content-area').insertAdjacentHTML('afterbegin',
                        '<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm font-medium"><i class="fa-solid fa-check-circle mr-2"></i>Google Search Console connected. Your properties are loaded.</div>');
                } else if (gscParam === 'denied') {
                    history.replaceState({}, '', window.location.pathname);
                    document.getElementById('content-area').insertAdjacentHTML('afterbegin',
                        '<div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm">Access was denied. You can connect again from the banner below.</div>');
                } else if (gscParam === 'config_missing') {
                    history.replaceState({}, '', window.location.pathname);
                    document.getElementById('content-area').insertAdjacentHTML('afterbegin',
                        '<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">Copy <code>config.sample.php</code> to <code>config.php</code> and add your Google OAuth credentials.</div>');
                }
                setInterval(() => {
                    DB.clients.forEach(c => {
                        (c.tasks || []).forEach(t => { if(t.active) t.timeSpent += 1; });
                    });
                }, 60000);
            },

            router: (route) => {
                document.querySelectorAll('.nav-item').forEach(e => e.classList.remove('active'));
                document.getElementById(`nav-${route}`).classList.add('active');
                const area = document.getElementById('content-area');
                
                if(route === 'dashboard') area.innerHTML = UI.renderDashboard(false);
                else if(route === 'watchlist') area.innerHTML = UI.renderDashboard(true);
                else if(route === 'sops') area.innerHTML = UI.renderSOPs();
                else if(route === 'outreach') {
                    area.innerHTML = UI.renderOutreach();
                    App.setTemplate('guest'); // Default state
                }
                else area.innerHTML = '<div class="p-10 text-center text-gray-400">Module Loaded</div>';
            },

            // --- OUTREACH LOGIC ---
            runOperator: () => {
                const niche = document.getElementById('op-niche').value;
                const footprint = document.getElementById('op-footprint').value;
                const city = document.getElementById('op-city').value;
                const query = city ? `${city} ${niche} ${footprint}` : `${niche} ${footprint}`;
                window.open('https://www.google.com/search?q=' + encodeURIComponent(query), '_blank');
            },

            setTemplate: (type) => {
                App.currentTemplate = type;
                App.updateEmailPreview();
            },

            updateEmailPreview: () => {
                const name = document.getElementById('gen-name').value || "[Name]";
                const site = document.getElementById('gen-site').value || "[Site]";
                const topic = document.getElementById('gen-topic').value || "[Topic/City]";
                const myname = document.getElementById('gen-myname').value || "Gene";

                let body = "";
                if(App.currentTemplate === 'guest') {
                    body = `Subject: Quick question about ${site}\n\nHi ${name},\n\nI've been reading ${site} for a while and really liked your recent work on real estate trends.\n\nI run a local investment firm in ${topic} and I'm seeing some interesting data regarding off-market sales. \n\nI'd love to contribute a unique article about this for your audience. No fluff, just real market data.\n\nAre you open to a draft?\n\nBest,\n${myname}`;
                } else if (App.currentTemplate === 'link') {
                    body = `Subject: Broken resource on ${site}\n\nHi ${name},\n\nI was browsing your resources page today and noticed a broken link.\n\nIt looks like the link to '...' is 404ing.\n\nI actually have a similar guide on ${topic} that is up to date if you want to swap it out.\n\nThanks,\n${myname}`;
                } else if (App.currentTemplate === 'pr') {
                    body = `Subject: Story idea for ${site}: ${topic}\n\nHi ${name},\n\nI saw your coverage on the housing market in ${topic}.\n\nAs a local buyer, I'm seeing a weird trend: [Insert Trend].\n\nIf you ever need a quote or data from the 'boots on the ground' perspective for a future story, I'm happy to help.\n\nBest,\n${myname}`;
                }

                const preview = document.getElementById('email-preview');
                if(preview) preview.innerText = body;
            },

            copyEmail: () => {
                const text = document.getElementById('email-preview').innerText;
                navigator.clipboard.writeText(text);
                alert("Email copied to clipboard!");
            },

            // --- SOP CONTROLLER ---
            loadSOP: (id) => {
                const sop = DB.sopLibrary.articles.find(a => a.id === id);
                document.querySelectorAll('.sop-nav-item').forEach(e => e.classList.remove('active'));
                document.getElementById(`sop-nav-${id}`).classList.add('active');
                document.getElementById('sop-viewer').innerHTML = UI.renderSOPDetail(sop);
            },

            toggleViewMode: (mode) => {
                DB.viewMode = mode;
                document.getElementById('view-ops').className = mode === 'ops' ? 'px-4 py-1.5 text-xs font-bold rounded shadow-sm bg-white text-slate-800 transition-all' : 'px-4 py-1.5 text-xs font-bold rounded text-slate-500 hover:text-slate-800 transition-all';
                document.getElementById('view-kpi').className = mode === 'kpi' ? 'px-4 py-1.5 text-xs font-bold rounded shadow-sm bg-white text-slate-800 transition-all' : 'px-4 py-1.5 text-xs font-bold rounded text-slate-500 hover:text-slate-800 transition-all';
                App.router('dashboard');
            },

            openClient: (id) => {
                const client = DB.clients.find(c => c.id === id);
                document.getElementById('page-title').innerHTML = `<button onclick="App.router('dashboard')" class="text-slate-400 mr-2 hover:text-slate-800"><i class="fa-solid fa-arrow-left"></i></button> ${client.name}`;
                document.getElementById('content-area').innerHTML = UI.renderCockpit(client);
                App.switchTab('performance', id);
            },

            switchTab: (tab, id) => {
                const client = DB.clients.find(c => c.id === id);
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.getElementById(`tab-${tab}`).classList.add('active');
                document.getElementById('tab-viewport').innerHTML = UI.getTabContent(tab, client);

                // Initialize features dependent on DOM
                if(tab === 'performance') App.renderCharts(client);
                if(tab === 'content') setTimeout(() => App.checkContent(id), 100);
                if(tab === 'tech') setTimeout(() => App.renderTechCharts(client), 100);
            },

            toggleStatus: (id, type) => {
                const client = DB.clients.find(c => c.id === id);
                const states = ['Pending', 'Done', 'Issue'];
                client.status[type] = states[(states.indexOf(client.status[type]) + 1) % states.length];
                App.router('dashboard');
            },

            // --- INTERACTIVE FEATURES (V2 Logic) ---
            toggleTimer: (cid, tid) => {
                const c = DB.clients.find(x => x.id === cid);
                const t = c.tasks.find(x => x.id === tid);
                t.active = !t.active;
                App.switchTab('workflow', cid);
            },

            moveTask: (cid, tid, dir) => {
                const c = DB.clients.find(x => x.id === cid);
                const t = c.tasks.find(x => x.id === tid);
                const s = ['todo', 'inprogress', 'done'];
                const idx = s.indexOf(t.status);
                const nIdx = dir === 'next' ? idx + 1 : idx - 1;
                if(nIdx >= 0 && nIdx < s.length) {
                    t.status = s[nIdx];
                    if(t.status === 'done') t.active = false;
                    App.switchTab('workflow', cid);
                }
            },

            addNote: (cid) => {
                const txt = document.getElementById('new-note').value;
                if(txt) {
                    const c = DB.clients.find(x => x.id === cid);
                    c.logs.push({ type: "user", user: DB.currentUser, action: "Manual Note", time: new Date().toLocaleString(), details: txt });
                    App.switchTab('logs', cid);
                }
            },

            checkContent: (cid) => {
                const client = DB.clients.find(x => x.id === cid);
                const text = document.getElementById('document-editor').innerText.toLowerCase();
                let foundCount = 0;
                
                client.ai.entities.forEach(entity => {
                    const cleanEntity = entity.replace(/\s/g, '');
                    const el = document.getElementById(`entity-${cleanEntity}`);
                    
                    if (text.includes(entity.toLowerCase())) {
                        if(el) { 
                            el.classList.add('found'); 
                            el.innerHTML = `<span class="text-emerald-700 font-bold">${entity}</span><i class="fa-solid fa-check text-emerald-600 text-xs"></i>`;
                        }
                        foundCount++;
                    } else {
                        if(el) { 
                            el.classList.remove('found'); 
                            el.innerHTML = `<span class="text-slate-600 font-medium">${entity}</span><i class="fa-regular fa-circle text-slate-300"></i>`;
                        }
                    }
                });

                const score = Math.round((foundCount / client.ai.entities.length) * 100);
                const circle = document.getElementById('content-score-circle');
                const bar = document.getElementById('progress-bar');
                
                if(circle) {
                    circle.innerText = score;
                    circle.className = `w-10 h-10 rounded-full border-4 flex items-center justify-center font-bold text-sm ${score > 80 ? 'border-emerald-200 text-emerald-600' : (score > 50 ? 'border-orange-200 text-orange-500' : 'border-red-200 text-red-500')}`;
                }
                if(bar) {
                    bar.style.width = `${score}%`;
                    bar.className = `h-full transition-all duration-500 ${score > 80 ? 'bg-emerald-500' : (score > 50 ? 'bg-orange-500' : 'bg-red-500')}`;
                }
            },

            // --- CHARTS ---
            renderCharts: (client) => {
                setTimeout(() => {
                    const ctxImp = document.getElementById('chartImpressions');
                    if(ctxImp) {
                        new Chart(ctxImp, {
                            type: 'line',
                            data: {
                                labels: client.kpi.history.map(h => h.day),
                                datasets: [{ label: 'Traffic', data: client.kpi.history.map(h => h.imp), borderColor: '#0ea5e9', tension: 0.4, fill: true, backgroundColor: '#e0f2fe', borderWidth: 2, pointRadius: 0 }]
                            },
                            options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } }, responsive: true, maintainAspectRatio: false }
                        });
                    }
                    const ctxLeads = document.getElementById('chartLeads');
                    if(ctxLeads) {
                        new Chart(ctxLeads, {
                            type: 'bar',
                            data: {
                                labels: client.kpi.history.map(h => h.day),
                                datasets: [{ label: 'Leads', data: client.kpi.history.map(h => h.leads), backgroundColor: '#4f46e5', borderRadius: 2 }]
                            },
                            options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } }, responsive: true, maintainAspectRatio: false }
                        });
                    }
                }, 50);
            },
            
            renderTechCharts: (client) => {
                const ctx = document.getElementById('chartAnchors');
                if(ctx) {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Branded', 'Topic', 'Exact Match'],
                            datasets: [{ data: client.assets.backlinks.distribution, backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'], borderWidth: 0 }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: {size: 10} } } }, cutout: '75%' }
                    });
                }
            }
        };

        window.addEventListener('DOMContentLoaded', App.init);
    </script>
</body>
</html>