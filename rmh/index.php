<?php
/**
 * RMH | Rank Maid Marketing Hub - Master V27
 * Core Application File (index.php)
 */

// 1. DATABASE CONNECTION
require_once __DIR__ . '/api/db.php';

$db_connected = isset($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMH | Rank Maid Marketing Hub - Master V27</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="h-screen flex overflow-hidden text-sm">

    <aside class="w-64 bg-slate-900 text-slate-400 flex flex-col shadow-2xl z-20 flex-shrink-0">
        <div class="h-16 flex items-center px-6 bg-slate-950 border-b border-slate-800">
            <i class="fa-solid fa-layer-group text-orange-600 text-xl mr-3"></i>
            <div>
                <h1 class="font-black text-white text-xl tracking-tight">RMH</h1>
                <p class="text-[9px] uppercase font-bold text-slate-500 tracking-wider">Master V27.0</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 space-y-1 pb-20">
            <div class="px-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Core Operations</div>
            <div class="px-3 mb-2">
                <div onclick="App.router('global')" id="nav-global" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-r-md active">
                    <i class="fa-solid fa-earth-americas w-5 text-center mr-3"></i> Global Command
                </div>
                <div onclick="App.router('goals')" id="nav-goals" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-r-md">
                    <i class="fa-solid fa-bullseye w-5 text-center mr-3"></i> Goals & Alignment
                </div>
                <div onclick="App.router('knowledge')" id="nav-knowledge" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-r-md">
                    <i class="fa-solid fa-book-open w-5 text-center mr-3"></i> Information Center
                </div>
            </div>

            <div class="px-6 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Paid Channels</div>
            <div class="px-3 space-y-1">
                <div onclick="App.router('facebook')" id="nav-facebook" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md">
                    <i class="fa-brands fa-facebook w-5 text-center mr-3"></i> Facebook Ads
                </div>
                <div onclick="App.router('google')" id="nav-google" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md">
                    <i class="fa-brands fa-google w-5 text-center mr-3"></i> Google Ads
                </div>
                <div onclick="App.router('reddit')" id="nav-reddit" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-brands fa-reddit w-5 text-center mr-3"></i> Reddit Ads</div>
                <div onclick="App.router('quora')" id="nav-quora" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-brands fa-quora w-5 text-center mr-3"></i> Quora Ads</div>
                <div onclick="App.router('taboola')" id="nav-taboola" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-solid fa-bullhorn w-5 text-center mr-3"></i> Taboola</div>
                <div onclick="App.router('adroll')" id="nav-adroll" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-solid fa-rectangle-ad w-5 text-center mr-3"></i> AdRoll</div>
            </div>

            <div class="px-6 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Outreach</div>
            <div class="px-3 space-y-1">
                <div onclick="App.router('linkedin')" id="nav-linkedin" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-brands fa-linkedin w-5 text-center mr-3"></i> LinkedIn</div>
                <div onclick="App.router('email')" id="nav-email" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-solid fa-envelope w-5 text-center mr-3"></i> Email Database</div>
                <div onclick="App.router('propstream')" id="nav-propstream" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-solid fa-house-signal w-5 text-center mr-3"></i> PropStream</div>
                <div onclick="App.router('craigslist')" id="nav-craigslist" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-solid fa-list w-5 text-center mr-3"></i> Craigslist</div>
                <div onclick="App.router('biggerpockets')" id="nav-biggerpockets" class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-r-md"><i class="fa-brands fa-get-pocket w-5 text-center mr-3"></i> BiggerPockets</div>
            </div>
        </nav>
        
        <div class="p-4 bg-slate-950 border-t border-slate-800 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white text-xs font-bold">JD</div>
            <div>
                <p class="text-xs text-white font-bold">Ops Lead</p>
                <p class="text-[10px] <?php echo $db_connected ? 'text-green-400' : 'text-red-400'; ?>">
                    ● Live Mode <?php echo $db_connected ? '| connected' : '| disconnected'; ?>
                </p>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative h-full bg-[#F3F4F6]">
        <header class="h-16 bg-white border-b border-gray-200 flex justify-between items-center px-8 z-10 shadow-sm">
            <div>
                <h2 id="page-title" class="text-xl font-bold text-gray-800">Global Command</h2>
                <p id="page-subtitle" class="text-xs text-gray-500 font-medium">Enterprise View</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Live Sync</p>
                    <p class="text-xs font-mono text-emerald-600 font-bold">UPDATED: JUST NOW</p>
                </div>
                <button onclick="App.simulateUpdate()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow-lg shadow-orange-100 transition-all text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-rotate"></i> Refresh Data
                </button>
            </div>
        </header>

        <div id="content-area" class="flex-1 overflow-y-auto p-8 fade-in"></div>
    </main>

    <div id="taskModal" class="fixed inset-0 modal-overlay z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl modal-content overflow-hidden border border-gray-200">
            <div class="bg-slate-900 px-8 py-6 flex justify-between items-center border-b border-slate-700">
                <div>
                    <h3 class="text-xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-brain text-orange-500"></i> Strategic Analysis
                    </h3>

                     <div style="visibility: collapse">
                        <div class="flex items-center gap-3 mt-1">
                            <p class="text-slate-400 text-xs">RMH Intelligence Engine v2.5</p>
                            <div class="flex items-center gap-2">
                            <div style="visibility: collapse">
                            <label for="modal_start_date" class="text-slate-500 text-[10px] uppercase font-bold">From:</label>
                                <input type="date" id="modal_start_date" class="bg-slate-800 text-white text-xs px-2 py-1 rounded border border-slate-700 focus:outline-none focus:border-orange-500">
                                <label for="modal_end_date" class="text-slate-500 text-[10px] uppercase font-bold">To:</label>
                                <input type="date" id="modal_end_date" class="bg-slate-800 text-white text-xs px-2 py-1 rounded border border-slate-700 focus:outline-none focus:border-orange-500">
                                <button id="apply_date_filter" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs font-bold transition-all">Apply</button>
                            </div>
                            </div>
                        </div>
                    </div>


                    <p class="text-slate-400 text-xs mt-1">RMH Intelligence Engine v2.5</p>
                </div>
                <button onclick="document.getElementById('taskModal').classList.add('hidden')" class="text-gray-400 hover:text-white bg-slate-800 hover:bg-slate-700 w-8 h-8 rounded-full flex items-center justify-center transition-all"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-0" id="modal-body"></div>
        </div>
    </div>

    <script>
        // 1. DATA STORE (EXACT FROM GUIDE.HTML)
        const DB = {
            facebook: { 
                id: 'facebook', name: 'Facebook Ads', type: 'paid', color: '#1877F2', goals: {targetCPA: 175, targetROAS: 4.0}, 
                metrics: { spend: {current:4200, prev:3800}, leads: {current:120, prev:95}, bookings: {current:18, prev:12}, revenue: {current:25000, prev:18000} }, 
                campaigns: [{id:'C1',name:'Prospecting_Broad_V3',status:'active',spend:2800,leads:85,bookings:12,revenue:18000}, {id:'C2',name:'Retargeting_Web',status:'active',spend:900,leads:30,bookings:6,revenue:7000}], 
                optimizations: [
                    { id: 101, title: 'Scale "Prospecting_Broad" Budget', type: 'Scale Opportunity', confidence: 94, rootCause: 'Campaign has sustained ROAS > 6.0x for 14 days.', projection: { leads: '+12 Leads/mo', revenue: '+$4,500 Revenue' }, instruction: '1. Navigate to <strong>Ads Manager</strong>.\n2. Select <strong>Prospecting_Broad_V3</strong>.\n3. Increase Daily Budget from <strong>$100 to $120</strong> (+20%).' }
                ],
                history: {roas:[2.1, 2.4, 3.8, 3.2, 4.0, 5.8, 6.2]} 
            },
            google: { id: 'google', name: 'Google Ads', type: 'paid', color: '#EA4335', goals: {targetCPA: 175, targetROAS: 3.5}, metrics: { spend: {current:3100, prev:3000}, leads: {current:45, prev:50}, bookings: {current:8, prev:10}, revenue: {current:12000, prev:15000} }, campaigns: [{id:'G1',name:'Brand_Search',status:'active',spend:2100,leads:35,bookings:6,revenue:10000}], optimizations: [], history: {roas:[4.1, 4.2, 3.8, 3.5, 3.2, 3.0, 3.1]} },
            
            goals: {
                vision: "To be the dominant SEO & Lead Generation partner for Real Estate Investors, filling sales calendars with high-intent appointments.",
                targets: { appointments: { target: 45, current: 41 }, caq: { target: 175, current: 168 } },
                scorecard: [
                    { role: 'VA / Operator', metric: 'Data Accuracy', target: '100% Match', consequence: 'Daily Audit' },
                    { role: 'Sales Rep', metric: 'Appts Attended', target: '45 / Week', consequence: 'Retraining' },
                    { role: 'Media Buyer', metric: 'CAQ (Cost/Appt)', target: '< $175', consequence: 'Budget Freeze' }
                ]
            },

            library: {
                algorithms: [
                    { title: "Facebook Algorithm for REI", icon: "fa-brands fa-facebook", color: "border-blue-500", summary: "Meta's AI optimizes for engagement.", details: `<h4>How the Algorithm Thinks</h4><p>Meta's algorithm doesn't care about "Real Estate." It cares about <strong>user intent signals</strong>.</p><h4>The Learning Phase</h4><p>Every ad set requires 50 conversions/week to exit Learning.</p><h4>Broad Targeting</h4><p>Use broad targeting and let creative do the filtering.</p>` },
                    { title: "Google Ads Quality Score", icon: "fa-brands fa-google", color: "border-red-500", summary: "Relevance = Lower Cost.", details: `<h4>The Formula</h4><p>Ad Rank = Bid × Quality Score.</p>` },
                    { title: "SEO: Local Pack & E-E-A-T", icon: "fa-solid fa-map-location-dot", color: "border-emerald-500", summary: "Map Pack drive calls.", details: `<h4>Execution</h4><p>NAP consistency is vital.</p>` }
                ],
                sops: [
                    { title: 'Scaling Budget', category: 'Optimization', steps: ['Check ROAS > 3.0x', 'Increase by exactly 20%'], details: "Increases >20% risk algorithm reset." },
                    { title: 'Fixing High CPA', category: 'Emergency', steps: ['Check CTR < 1%', 'Pause if CPA > $250'], details: "Check Landing Page Conversion Rate." }
                ],
                scripts: [
                    { title: "SMS: New Web Lead", context: "Form follow-up.", content: "Hi [Name], I saw you looked for a cash offer on [Address]. Free now?", objections: "No reply? Text 10 mins later." }
                ]
            }
        };

        // 2. LOGIC & UI (EXACT FROM GUIDE.HTML)
        const Logic = {
            getChannelAggregates: (id) => { const ch = DB[id]; return ch ? { ...ch.metrics, cpa: ch.metrics.leads.current>0?ch.metrics.spend.current/ch.metrics.leads.current:0, roi: ch.metrics.spend.current>0?((ch.metrics.revenue.current-ch.metrics.spend.current)/ch.metrics.spend.current)*100:0 } : null; },
            getGlobalStats: () => { return { revenue: 37000, spend: 7300, leads: 165, bookings: 26, cpa: 280, avgDeal: 7500 }; }
        };

        const UI = {
            usd: (n) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(n),
            num: (n) => new Intl.NumberFormat('en-US').format(n),
            card: (t, v, c, s) => `<div class="glass-panel p-5"><p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">${t}</p><h3 class="text-2xl font-black ${c}">${v}</h3><p class="text-[10px] text-gray-400 mt-1">${s}</p></div>`,
            feedItem: (s, t, m, a) => `<div class="border-b border-gray-700/50 pb-3 last:border-0"><div class="flex justify-between items-center mb-1"><span class="text-xs font-bold text-gray-300 flex items-center"><span class="w-2 h-2 rounded-full ${t==='good'?'bg-emerald-500':'bg-red-500'} inline-block mr-2"></span>${s}</span><button onclick="App.openStrategicModal('${s.toLowerCase()}')" class="text-[9px] bg-orange-600 text-white px-2 rounded">${a}</button></div><p class="text-xs text-slate-400 pl-4">${m}</p></div>`,
            
            renderGlobal: () => {
                const g = Logic.getGlobalStats();
                return `
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                        ${UI.card('Total Revenue', UI.usd(g.revenue), 'text-emerald-600', 'Gross Revenue')}
                        ${UI.card('Total Spend', UI.usd(g.spend), 'text-slate-600', 'Ad Spend')}
                        ${UI.card('Blended CAQ', UI.usd(g.cpa), 'text-orange-600', 'Cost/Booking')}
                        ${UI.card('Pipeline Volume', UI.num(g.leads), 'text-gray-900', 'Total Leads')}
                        ${UI.card('Avg Deal Size', UI.usd(g.avgDeal), 'text-blue-600', 'Est. Value')}
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                        <div class="lg:col-span-2 glass-panel p-6"><h3 class="font-bold text-gray-700 mb-4">Global Revenue Velocity</h3><div class="h-72"><canvas id="mainChart"></canvas></div></div>
                        <div class="bg-slate-800 text-white p-6 rounded-xl shadow-lg border-l-4 border-orange-600">
                            <h3 class="font-bold text-slate-400 text-xs uppercase tracking-widest mb-6">AI Feed</h3>
                            <div class="space-y-4 text-sm">${UI.feedItem('Facebook','good','Scale Warning: ROI > 400%','Approve')}${UI.feedItem('Google','bad','CAQ spiked to $190','Fix')}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="glass-panel p-6"><h3 class="font-bold text-gray-700 mb-4">Source Mix</h3><div class="h-60 flex justify-center"><canvas id="mixChart"></canvas></div></div>
                        <div class="lg:col-span-2 glass-panel overflow-hidden"><table class="w-full text-left text-sm text-gray-600"><thead class="bg-gray-50"><tr><th class="px-6 py-3">Channel</th><th>Rev</th><th>Spend</th><th>ROI</th></tr></thead><tbody><tr class="hover:bg-orange-50 cursor-pointer" onclick="App.router('facebook')"><td class="px-6 py-4 font-bold">Facebook Ads</td><td class="text-emerald-600 font-bold">$25,000</td><td>$4,200</td><td>495%</td></tr></tbody></table></div>
                    </div>`;
            }
        };

        const App = {
            router: (route) => {
                document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
                const nav = document.getElementById(`nav-${route}`); if(nav) nav.classList.add('active');
                const container = document.getElementById('content-area');

                if(route === 'global') {
                    container.innerHTML = UI.renderGlobal();
                    setTimeout(() => {
                        new Chart(document.getElementById('mainChart'),{type:'line',data:{labels:['M','T','W','T','F','S','S'],datasets:[{data:[65,59,80,81,56,95,110],borderColor:'#ea580c',backgroundColor:'#ea580c10',fill:true}]},options:{maintainAspectRatio:false,plugins:{legend:false}}});
                        new Chart(document.getElementById('mixChart'),{type:'doughnut',data:{labels:['Paid','Organic'],datasets:[{data:[300,150],backgroundColor:['#ea580c','#10b981'],borderWidth:0}]},options:{cutout:'75%',plugins:{legend:{position:'bottom'}}}});
                    }, 50);
                } else if(route === 'goals') {
                    const g = DB.goals;
                    container.innerHTML = `<div class="mb-8"><div class="glass-panel p-8 bg-gradient-to-r from-slate-900 via-orange-900 to-slate-900 text-white shadow-2xl relative overflow-hidden"><h1 class="text-4xl font-black mt-4 mb-2 tracking-tight">SEO for Real Estate Investors</h1><p class="text-lg text-orange-100 max-w-3xl leading-relaxed">"${g.vision}"</p></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8"><div class="glass-panel p-8 border-l-8 border-emerald-500"><div><p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Weekly North Star</p><h2 class="text-5xl font-black text-gray-800">${g.targets.appointments.current}</h2><p class="text-sm font-medium text-gray-500 mt-2">Target: ${g.targets.appointments.target} Appts</p></div></div><div class="glass-panel p-8 border-l-8 border-orange-500"><div><p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Guardrail (CAQ)</p><h2 class="text-5xl font-black text-emerald-600">$${g.targets.caq.current}</h2><p class="text-sm font-medium text-gray-500 mt-2">Target: under $${g.targets.caq.target}</p></div></div></div><div class="glass-panel p-8"><h3 class="font-bold text-gray-800 mb-6">Scorecard</h3><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="p-4">Role</th><th class="p-4">Metric</th><th class="p-4">Target</th><th class="p-4">Consequence</th></tr></thead><tbody>${g.scorecard.map(s=>`<tr><td class="p-4 font-bold">${s.role}</td><td class="p-4">${s.metric}</td><td class="p-4 text-emerald-600 font-bold">${s.target}</td><td class="p-4 text-xs text-red-500">${s.consequence}</td></tr>`).join('')}</tbody></table></div>`;
                } else if(route === 'knowledge') {
                    const lib = DB.library;
                    const tab = window.currentTab || 'algo';
                    const tabs = `<div class="flex gap-4 border-b border-gray-200 mb-8"><button onclick="window.currentTab='algo'; App.router('knowledge')" class="tab-btn ${tab==='algo'?'active':''}">Algorithms</button><button onclick="window.currentTab='sops'; App.router('knowledge')" class="tab-btn ${tab==='sops'?'active':''}">SOP Library</button><button onclick="window.currentTab='scripts'; App.router('knowledge')" class="tab-btn ${tab==='scripts'?'active':''}">Script Vault</button></div>`;
                    let content = '';
                    if(tab === 'algo') content = `<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">${lib.algorithms.map((a, i)=>`<div class="glass-panel p-6 border-t-4 ${a.color} kb-card" onclick="document.getElementById('algo-details-${i}').classList.toggle('hidden')"><div class="flex items-center gap-3 mb-4"><i class="${a.icon} text-2xl text-gray-600"></i><div><h4 class="font-bold text-gray-900 text-lg m-0">${a.title}</h4><p class="text-xs text-gray-500">${a.summary}</p></div></div><div id="algo-details-${i}" class="hidden prose text-sm text-gray-600 border-t pt-4 bg-gray-50 p-4 rounded-lg">${a.details}</div><p class="text-center text-xs text-orange-600 font-bold mt-2">Click to Expand</p></div>`).join('')}</div>`;
                    else if(tab === 'sops') content = `<div class="space-y-4">${lib.sops.map((s,i)=>`<div class="glass-panel p-6"><h4 class="font-bold text-gray-800">${s.title}</h4><ol class="list-decimal list-inside text-sm text-gray-700">${s.steps.map(step=>`<li>${step}</li>`).join('')}</ol><div class="bg-blue-50 p-3 rounded mt-3 text-xs text-blue-800">${s.details}</div></div>`).join('')}</div>`;
                    else if(tab === 'scripts') content = `<div class="grid grid-cols-1 md:grid-cols-2 gap-6">${lib.scripts.map((s, i) => `<div class="glass-panel p-6"><h4 class="font-bold text-slate-800">${s.title}</h4><p class="font-mono text-sm text-slate-700 bg-orange-50 p-3 rounded border border-orange-100 mt-2">"${s.content}"</p><div class="mt-2 text-xs text-red-800"><strong>Objection:</strong> ${s.objections}</div></div>`).join('')}</div>`;
                    container.innerHTML = `<div class="mb-8"><h2 class="text-3xl font-black text-gray-900">Knowledge Hub</h2><p class="text-gray-500">Operational Intelligence</p></div>${tabs}${content}`;
                } else {
                    const data = DB[route];
                    if(data) {
                        const m = data.metrics;
                        container.innerHTML = `<div class="mb-6"><h2 class="text-2xl font-black text-gray-800">${data.name}</h2><p class="text-xs text-gray-500 font-medium">Channel Intelligence</p></div><div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">${UI.card('AD SPEND', UI.usd(m.spend.current), 'text-slate-800', '')}${UI.card('LEADS', UI.num(m.leads.current), 'text-slate-800', '')}${UI.card('BOOKED APPTS', UI.num(m.bookings.current), 'text-orange-600', '')}${UI.card('COST PER BOOK', UI.usd(m.spend.current / m.bookings.current), 'text-red-600', '')}${UI.card('REVENUE', UI.usd(m.revenue.current), 'text-emerald-600', '')}</div><div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8"><div class="lg:col-span-2 glass-panel p-6"><h3 class="font-bold text-gray-700 mb-4">Efficiency Trend</h3><div class="h-64"><canvas id="channelChart"></canvas></div></div><div class="glass-panel p-0 overflow-hidden border-2 border-orange-600"><div class="bg-white px-6 py-4 border-b border-orange-600"><div class="flex items-center justify-between"><h3 class="font-black text-gray-800 flex items-center gap-2"><i class="fa-solid fa-sparkles text-orange-600 text-lg"></i> Genius AI</h3><span class="text-xs font-bold text-orange-600">2 Ideas</span></div></div><div class="p-6 space-y-4">${data.optimizations.map((opt, i) => `<div class="border-b border-gray-200 pb-4 last:border-0"><div class="flex items-center justify-between mb-2"><span class="bg-orange-100 text-orange-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">${opt.type}</span><span class="text-[10px] font-bold text-gray-400">${opt.confidence}% Conf.</span></div><h4 class="font-bold text-gray-900 text-sm mb-3">${opt.title}</h4><button onclick="App.openStrategicModal('${route}')" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 rounded text-xs transition-all">Analyze</button></div>`).join('')}</div></div></div><div class="glass-panel overflow-hidden mt-8"><div class="bg-gray-50 px-6 py-4 border-b border-gray-200"><h3 class="font-bold text-gray-800">Campaign Manager</h3></div><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-xs font-bold text-gray-600">Status</th><th class="px-6 py-3 text-xs font-bold text-gray-600">Name</th><th class="px-6 py-3 text-xs font-bold text-gray-600">Spend</th><th class="px-6 py-3 text-xs font-bold text-gray-600">Leads</th><th class="px-6 py-3 text-xs font-bold text-gray-600">Bookings</th><th class="px-6 py-3 text-xs font-bold text-gray-600">Revenue</th></tr></thead><tbody>${data.campaigns.map(c=>`<tr class="border-t border-gray-100 hover:bg-orange-50"><td class="px-6 py-3 text-xs"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>ACTIVE</td><td class="px-6 py-3 font-bold text-gray-800 text-sm">${c.name}</td><td class="px-6 py-3 text-gray-600">${UI.usd(c.spend)}</td><td class="px-6 py-3 text-gray-600">${c.leads}</td><td class="px-6 py-3 text-orange-600 font-bold">${c.bookings}</td><td class="px-6 py-3 text-emerald-600 font-bold">${UI.usd(c.revenue)}</td></tr>`).join('')}</tbody></table></div>`;
                        setTimeout(() => { new Chart(document.getElementById('channelChart'),{type:'line',data:{labels:['M','T','W','T','F','S','S'],datasets:[{data:data.history.roas,borderColor:'#ea580c',backgroundColor:'#ea580c10',fill:true}]},options:{maintainAspectRatio:false,plugins:{legend:false}}}); }, 50);
                    } else {
                        container.innerHTML = `<div class="p-20 text-center text-gray-400">Section: ${route} integration pending data source.</div>`;
                    }
                }
            },
            openStrategicModal: (cid) => {
                const opt = DB[cid].optimizations[0];
                if (!opt) return;

                const modal = document.getElementById('taskModal');
                const modalBody = document.getElementById('modal-body');
                const startDateInput = document.getElementById('modal_start_date');
                const endDateInput = document.getElementById('modal_end_date');
                const applyFilterBtn = document.getElementById('apply_date_filter');

                // Set default dates to the current month if not already set
                if (!startDateInput.value && !endDateInput.value) {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = today.getMonth();
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    startDateInput.value = firstDay.toISOString().split('T')[0];
                    endDateInput.value = lastDay.toISOString().split('T')[0];
                }
                
                // Show loading state
                modal.classList.remove('hidden');
                modalBody.innerHTML = `<div class="p-8 text-center"><p class="text-gray-500">Loading real database analysis...</p></div>`;
                
                // Fetch real data from database
                const fetchAnalysisData = () => {
                    const startDate = startDateInput.value;
                    const endDate = endDateInput.value;
                    let apiUrl = `api/facebook_analysis.php?action=getDetailedAnalysis&assumed_value=150000`;
                    if (startDate) apiUrl += `&start_date=${startDate}`;
                    if (endDate) apiUrl += `&end_date=${endDate}`;

                    fetch(apiUrl)
                    .then(response => response.json())
                    .then(result => {
                        if (!result.success) throw new Error(result.error);
                        
                        const data = result.data;
                        const sustained = data.sustained_found;
                        const bestWindow = data.best_window;
                        
                        if (!bestWindow) {
                            throw new Error('No data available for analysis');
                        }
                        
                        // Build daily breakdown table
                        let dailyBreakdownHTML = `<table class="w-full text-xs"><thead class="bg-gray-100"><tr><th class="p-2 text-left">Date</th><th class="p-2 text-right">Results</th><th class="p-2 text-right">Spend</th><th class="p-2 text-right">ROAS</th></tr></thead><tbody>`;
                        
                        // Show last 14 days or all available days
                        const displayDays = data.daily_breakdown;
                        displayDays.forEach(day => {
                            const roasColor = day.roas > 6.0 ? 'text-emerald-600 font-bold' : 'text-red-600';
                            const spend = typeof day.spend === 'number' ? day.spend : parseFloat(day.spend);
                            const roas = typeof day.roas === 'number' ? day.roas : parseFloat(day.roas);
                            dailyBreakdownHTML += `<tr class="border-b"><td class="p-2">${day.date}</td><td class="p-2 text-right">${day.results}</td><td class="p-2 text-right">$${spend.toFixed(0)}</td><td class="p-2 text-right ${roasColor}">${roas.toFixed(2)}x</td></tr>`;
                        });
                        
                        dailyBreakdownHTML += `</tbody></table>`;
                        
                        // Calculate totals
                        const totalResults = data.daily_breakdown.reduce((sum, d) => sum + d.results, 0);
                        const totalSpend = data.daily_breakdown.reduce((sum, d) => sum + d.spend, 0);
                        const totalRevenue = totalResults * data.assumed_value_per_result;
                        const overallRoas = totalSpend > 0 ? (totalRevenue / totalSpend) : 0;
                        
                        // Build updated modal content
                        const modalContent = `<div class="grid grid-cols-3 divide-x divide-gray-100">
                            <div class="col-span-2 p-8">
                                <div class="flex items-center gap-3 mb-6">
                                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">${opt.type}</span>
                                    <span class="text-xs font-bold text-gray-500">Database Verified</span>
                                    <span class="flex items-center gap-1 text-xs font-bold text-gray-500">Confidence: ${opt.confidence}%</span>
                                </div>
                                <h4 class="text-2xl font-black text-gray-900 mb-4">${opt.title}</h4>
                                
                                <div class="mb-8">
                                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Root Cause</h5>
                                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-slate-300">
                                        <p class="text-gray-600 italic text-sm">${opt.rootCause}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tactical Instruction</h5>
                                    <div class="prose text-sm text-gray-600 leading-relaxed">${opt.instruction.replace(/\n/g, '<br>')}</div>
                                </div>

                            </div>
                            <div class="col-span-1 bg-slate-50 p-8 flex flex-col justify-between">
                                <div>
                                    <div class="mt-8">
                                        <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Projected Impact</h5>
                                        <div class="space-y-4">
                                            <div class="bg-white p-4 rounded shadow-sm border border-gray-100">
                                                <p class="text-[10px] font-bold uppercase text-gray-400">Additional Leads</p>
                                                <p class="text-lg font-black text-emerald-600">${opt.projection.leads}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded shadow-sm border border-gray-100">
                                                <p class="text-[10px] font-bold uppercase text-gray-400">Revenue</p>
                                                <p class="text-lg font-black text-emerald-600">${opt.projection.revenue}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-8 space-y-3">
                                    <button onclick="document.getElementById('taskModal').classList.add('hidden')" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-lg shadow-lg transition-all">Apply Changes</button>
                                     <button onclick="document.getElementById('taskModal').classList.add('hidden')" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-lg transition-all">Snooze</button>
                            </div>
                        </div>`;
                        
                        document.getElementById('modal-body').innerHTML = modalContent;
                    }).catch(error => {
                        console.error('Error fetching detailed analysis:', error);
                        modalBody.innerHTML = `<div class="p-8"><div class="bg-red-50 border border-red-200 p-4 rounded-lg"><p class="text-red-700 font-bold">Error Loading Analysis</p><p class="text-red-600 text-sm mt-2">${error.message}</p><p class="text-red-500 text-xs mt-3">Make sure XAMPP is running and the database connection is active.</p></div></div>`;
                    });
                };

                fetchAnalysisData(); // Initial fetch
                applyFilterBtn.onclick = fetchAnalysisData; // Re-fetch on button click
            },
            simulateUpdate: () => { location.reload(); },
            init: () => { App.router('global'); }
        };

        window.addEventListener('DOMContentLoaded', App.init);
    </script>
</body>
</html>