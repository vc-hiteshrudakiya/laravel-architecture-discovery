<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $data['project']['name'] }} — Architecture Dashboard</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<style>
/* ── Atlassian Light Theme CSS Variables ── */
:root{
  --bg:#F7F8F9; --bg-elevated:#FFFFFF; --bg-sunken:#F4F5F7; --bg-hover:#F4F5F7;
  --border:#DFE1E6; --border-strong:#B3BAC5; --grid-line:transparent;
  --text:#172B4D; --text-dim:#42526E; --text-faint:#6B778C;
  --cyan:#0052CC; --cyan-bright:#4C9AFF; --emerald:#00875A; --amber:#FF8B00; --rose:#DE350B; --violet:#6554C0; --sky:#0065FF;
  --shadow:0 4px 16px rgba(23,43,77,0.08),0 1px 3px rgba(23,43,77,0.06);
  --shadow-hover:0 8px 32px rgba(23,43,77,0.12),0 2px 8px rgba(23,43,77,0.08);
  --font-sans:'Inter',sans-serif; --font-mono:'JetBrains Mono',monospace;
  --ease:cubic-bezier(.22,.61,.36,1);
}

/* ── Body ── */
body{
  margin:0;background:var(--bg);color:var(--text);font-family:var(--font-sans);font-size:14.5px;-webkit-font-smoothing:antialiased;
}

/* ── Layout ── */
.atlas-layout{display:grid;grid-template-columns:264px 1fr;height:100vh;overflow:hidden;}
.content{overflow-y:auto;}
.content::-webkit-scrollbar{width:6px}.content::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:3px}

/* ── Radar Animation ── */
.radar{position:relative;width:18px;height:18px;display:inline-block;flex:none;}
.radar__ring{position:absolute;inset:0;border:1px solid var(--cyan);border-radius:50%;animation:radarPulse 2.4s var(--ease) infinite;}
.radar__ring--delay{animation-delay:1.2s;}
.radar__dot{position:absolute;inset:0;margin:auto;width:4px;height:4px;border-radius:50%;background:var(--cyan);}
.radar__sweep{position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,rgba(0,82,204,0.45),transparent 40%);animation:radarSpin 2.2s linear infinite;}
@keyframes radarPulse{0%{transform:scale(.5);opacity:.7;}100%{transform:scale(1.9);opacity:0;}}
@keyframes radarSpin{to{transform:rotate(360deg);}}
@keyframes spin{to{transform:rotate(360deg);}}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:none;}}
.p-6>*{animation:fadeUp .35s var(--ease) both;}

/* ── Sidebar ── */
.sidebar{background:var(--bg-elevated);border-right:1px solid var(--border);padding:24px 16px;display:flex;flex-direction:column;position:sticky;top:0;height:100vh;z-index:90;transition:transform .35s var(--ease);overflow-y:auto;}
.sidebar::-webkit-scrollbar{width:4px}.sidebar::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:2px}
.sidebar__brand{display:flex;align-items:center;gap:10px;padding:6px 8px 22px;}
.sidebar__brand .mark{width:34px;height:34px;border-radius:8px;background:linear-gradient(155deg,#0052CC,#4C9AFF);border:1px solid rgba(0,82,204,0.3);display:flex;align-items:center;justify-content:center;flex:none;}
.sidebar__brand div{line-height:1.25;}
.sidebar__brand strong{font-size:17px;letter-spacing:0.02em;color:var(--text);}
.sidebar__brand span{font-family:var(--font-mono);font-size:9.5px;letter-spacing:0.1em;color:var(--text-faint);text-transform:uppercase;}
.sidebar nav{flex:1;overflow-y:auto;}
.nav-group{margin-bottom:20px;}
.nav-group__label{font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--text-faint);padding:0 10px;margin-bottom:9px;display:block;}
.nav-item{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:7px;margin-bottom:2px;color:var(--text-dim);font-size:13.5px;font-weight:600;position:relative;transition:background .2s,color .2s;cursor:pointer;border:none;background:none;width:100%;text-align:left;}
.nav-item svg{width:17px;height:17px;flex:none;stroke:currentColor;}
.nav-item:hover{background:rgba(0,82,204,0.06);color:var(--cyan);}
.nav-item.nav-active{background:rgba(0,82,204,0.08);color:var(--cyan);}
.nav-item.nav-active::before{content:"";position:absolute;left:-16px;top:8px;bottom:8px;width:3px;background:var(--cyan);border-radius:2px;}
.nav-badge{margin-left:auto;font-family:var(--font-mono);font-size:10px;background:rgba(0,82,204,0.08);color:var(--cyan);padding:2px 7px;border-radius:20px;border:1px solid rgba(0,82,204,0.2);}
.sidebar__scan{border-top:1px solid var(--border);padding-top:16px;display:flex;align-items:center;gap:10px;margin-top:auto;}
.sidebar__scan div{line-height:1.3;}
.sidebar__scan strong{font-size:12.5px;display:block;color:var(--text);}
.sidebar__scan span.scan-label{font-size:11px;color:var(--text-faint);font-family:var(--font-mono);}

/* ── Topbar ── */
.topbar{position:sticky;top:0;z-index:60;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;padding:16px 30px;box-shadow:0 1px 0 var(--border);}
.breadcrumb{font-family:var(--font-mono);font-size:12px;color:var(--text-faint);display:flex;align-items:center;gap:8px;}
.breadcrumb b{color:var(--text);font-weight:600;}
.sync-pill{display:flex;align-items:center;gap:8px;font-family:var(--font-mono);font-size:11.5px;color:var(--text-dim);border:1px solid var(--border);border-radius:20px;padding:6px 12px 6px 10px;margin-left:auto;}
.sync-dot{width:6px;height:6px;border-radius:50%;background:var(--emerald);box-shadow:0 0 0 3px rgba(52,211,153,0.18);flex:none;}

/* ── Atlas Cards ── */
.atlas-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:22px;box-shadow:var(--shadow);}
.atlas-card__head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;}
.atlas-card__head h3{font-size:15.5px;font-weight:700;margin:0;}

/* ── KPI Cards ── */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;margin-bottom:22px;}
.kpi-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:20px;transition:transform .25s var(--ease),box-shadow .25s var(--ease),border-color .25s;cursor:default;box-shadow:var(--shadow);}
.kpi-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.kpi-card__icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex:none;margin-bottom:12px;}
.kpi-card__icon svg{width:17px;height:17px;stroke:currentColor;fill:none;}
.kpi-card__label{font-family:var(--font-mono);font-size:10.5px;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-faint);display:block;}
.kpi-card__num{font-family:var(--font-mono);font-size:28px;letter-spacing:-0.01em;margin-top:4px;display:block;}

/* ── Controller Cards ── */
.ctrl-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:22px;cursor:pointer;transition:transform .25s var(--ease),box-shadow .25s var(--ease),border-color .25s;box-shadow:var(--shadow);}
.ctrl-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-hover);border-color:rgba(255,139,0,0.4);}
.ctrl-card__icon{width:38px;height:38px;border-radius:9px;background:rgba(255,139,0,0.10);color:var(--amber);display:flex;align-items:center;justify-content:center;flex:none;}
.ctrl-card__icon svg{width:18px;height:18px;stroke:currentColor;fill:none;}
.ctrl-card__name{font-family:var(--font-mono);font-size:15px;font-weight:600;color:var(--text);}
.ctrl-card__ns{font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;}
.ctrl-stat{text-align:center;background:var(--bg-sunken);border-radius:8px;padding:10px 4px;}
.ctrl-stat b{font-family:var(--font-mono);font-size:17px;display:block;color:var(--text);}
.ctrl-stat span{font-family:var(--font-mono);font-size:9.5px;text-transform:uppercase;letter-spacing:.05em;color:var(--text-faint);}
.ctrl-chip{font-family:var(--font-mono);font-size:10px;padding:3px 7px;border-radius:5px;background:var(--bg-sunken);color:var(--text-dim);border:1px solid var(--border);}

/* ── Method Badges ── */
.method-get{background:rgba(0,82,204,0.10)!important;color:var(--cyan)!important;}
.method-post{background:rgba(0,135,90,0.10)!important;color:var(--emerald)!important;}
.method-put,.method-patch{background:rgba(101,84,192,0.10)!important;color:var(--violet)!important;}
.method-delete{background:rgba(222,53,11,0.10)!important;color:var(--rose)!important;}
.method-head,.method-options{background:rgba(107,119,140,0.12)!important;color:var(--text-faint)!important;}

/* ── Grade Badges ── */
.grade-a{background:rgba(0,135,90,0.10);color:var(--emerald);border:1px solid rgba(0,135,90,0.25);}
.grade-b{background:rgba(0,82,204,0.10);color:var(--cyan);border:1px solid rgba(0,82,204,0.25);}
.grade-c{background:rgba(255,139,0,0.10);color:var(--amber);border:1px solid rgba(255,139,0,0.25);}
.grade-d{background:rgba(255,139,0,0.12);color:#c05c00;border:1px solid rgba(255,139,0,0.3);}
.grade-f{background:rgba(222,53,11,0.10);color:var(--rose);border:1px solid rgba(222,53,11,0.25);}

/* ── Generic section card ── */
.card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;transition:transform .25s var(--ease),box-shadow .25s var(--ease),border-color .25s;box-shadow:var(--shadow);}
.card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}

/* ── Mermaid / Graph canvas bg overrides ── */
.mermaid svg{max-width:100%;height:auto;}
.g-node{transition:opacity .15s ease;}
.diag-tab{transition:background .15s,color .15s,border-color .15s;}

/* ── Tailwind overrides for light theme ── */
.bg-white{background:#FFFFFF!important;}
.bg-slate-50,.bg-slate-100{background:var(--bg-sunken)!important;}
.bg-slate-900,.bg-slate-800{background:#172B4D!important;}
.text-slate-800,.text-slate-900{color:var(--text)!important;}
.text-slate-500,.text-slate-400,.text-slate-600{color:var(--text-dim)!important;}
.text-slate-300{color:var(--text-faint)!important;}
.border-slate-100,.border-slate-200,.border-slate-700{border-color:var(--border)!important;}
.divide-slate-100>*+*{border-color:var(--border)!important;}

/* ── Form inputs ── */
input[type="search"],input[type="text"],select,textarea{
  background:#FFFFFF!important;
  border-color:var(--border)!important;
  color:var(--text)!important;
}
input[type="search"]::placeholder,input[type="text"]::placeholder{color:var(--text-faint)!important;}
select option{background:#FFFFFF;color:var(--text);}

/* ── Table rows ── */
tr.route-row:hover{background:rgba(0,82,204,0.04)!important;}
thead tr{background:var(--bg-sunken)!important;}

/* ── Buttons ── */
.atlas-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;font-family:var(--font-mono);font-size:12px;font-weight:600;border:1px solid var(--border);background:#FFFFFF;color:var(--text-dim);cursor:pointer;transition:border-color .2s,color .2s,background .2s,box-shadow .2s;}
.atlas-btn:hover{border-color:var(--border-strong);color:var(--text);box-shadow:var(--shadow);}
.atlas-btn--cyan{border-color:rgba(0,82,204,0.4);color:var(--cyan);background:rgba(0,82,204,0.06);}
.atlas-btn--cyan:hover{border-color:var(--cyan);background:rgba(0,82,204,0.10);}

/* ── Score bar ── */
.atlas-score-bar{height:4px;border-radius:2px;background:var(--bg-sunken);overflow:hidden;}
.atlas-score-fill{height:100%;border-radius:2px;background:linear-gradient(90deg,var(--cyan),var(--emerald));}

/* ── Section headings ── */
.sec-title{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.01em;}
.sec-sub{font-size:13px;color:var(--text-faint);font-family:var(--font-mono);margin-top:4px;}

/* ── Relation graph canvas ── */
#rg-canvas{background:var(--bg-sunken)!important;}
.relative.rounded-2xl.border{background:var(--bg-elevated)!important;border-color:var(--border)!important;}

/* ── Tailwind color overrides for JS-rendered detail panels ── */
.bg-indigo-50{background:rgba(79,70,229,0.12)!important;}
.bg-indigo-100{background:rgba(79,70,229,0.18)!important;}
.bg-blue-50{background:rgba(59,130,246,0.12)!important;}
.bg-blue-100{background:rgba(59,130,246,0.18)!important;}
.bg-green-50{background:rgba(34,197,94,0.12)!important;}
.bg-green-100{background:rgba(34,197,94,0.18)!important;}
.bg-teal-50{background:rgba(20,184,166,0.12)!important;}
.bg-purple-50{background:rgba(168,85,247,0.12)!important;}
.bg-purple-100{background:rgba(168,85,247,0.18)!important;}
.bg-pink-50{background:rgba(236,72,153,0.12)!important;}
.bg-orange-50{background:rgba(251,146,60,0.12)!important;}
.bg-orange-100{background:rgba(251,146,60,0.18)!important;}
.bg-amber-50{background:rgba(245,158,11,0.12)!important;}
.bg-amber-100{background:rgba(245,158,11,0.18)!important;}
.bg-red-50{background:rgba(239,68,68,0.12)!important;}
.bg-red-100{background:rgba(239,68,68,0.18)!important;}
.bg-emerald-50{background:rgba(16,185,129,0.12)!important;}
.bg-violet-50{background:rgba(139,92,246,0.12)!important;}
.bg-sky-50{background:rgba(14,165,233,0.12)!important;}

/* color overrides for text on these light backgrounds */
.text-indigo-600,.text-indigo-700{color:#818cf8!important;}
.text-blue-600,.text-blue-700{color:var(--sky)!important;}
.text-green-600,.text-green-700{color:var(--emerald)!important;}
.text-teal-600,.text-teal-700{color:#2dd4bf!important;}
.text-purple-600,.text-purple-700{color:var(--violet)!important;}
.text-pink-600,.text-pink-700{color:#f472b6!important;}
.text-orange-600,.text-orange-700{color:#fb923c!important;}
.text-amber-600,.text-amber-700,.text-amber-800{color:var(--amber)!important;}
.text-red-600,.text-red-700{color:var(--rose)!important;}
.text-emerald-600,.text-emerald-700{color:var(--emerald)!important;}
.text-violet-600,.text-violet-700{color:var(--violet)!important;}
.text-green-800{color:var(--emerald)!important;}
.text-amber-800{color:var(--amber)!important;}
.text-red-800,.text-red-900{color:var(--rose)!important;}

/* border color overrides */
.border-indigo-200{border-color:rgba(79,70,229,0.3)!important;}
.border-blue-200{border-color:rgba(59,130,246,0.3)!important;}
.border-green-200{border-color:rgba(34,197,94,0.3)!important;}
.border-amber-200{border-color:rgba(245,158,11,0.3)!important;}
.border-red-200{border-color:rgba(239,68,68,0.3)!important;}
.border-orange-200{border-color:rgba(251,146,60,0.3)!important;}
.border-purple-200{border-color:rgba(168,85,247,0.3)!important;}
.border-emerald-200{border-color:rgba(52,211,153,0.3)!important;}
.border-violet-200{border-color:rgba(139,92,246,0.3)!important;}

/* hover state overrides */
.hover\:bg-slate-50:hover{background:var(--bg-hover)!important;}
.hover\:bg-slate-100:hover{background:var(--bg-hover)!important;}
.hover\:bg-indigo-50:hover{background:rgba(79,70,229,0.18)!important;}
.hover\:bg-indigo-100:hover{background:rgba(79,70,229,0.25)!important;}

/* focused ring overrides (Tailwind focus:ring) */
.focus\:ring-indigo-300:focus{--tw-ring-color:rgba(0,82,204,0.4);}

/* rounded bg code elements */
code{background:var(--bg-sunken)!important;color:var(--cyan)!important;border:1px solid var(--border);border-radius:4px;padding:1px 5px;font-size:.9em;}

/* ── Models Page (redesigned) ── */
.mds-top-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px;}
.mds-top-stat{background:var(--bg-elevated);border:1px solid var(--border);border-radius:13px;padding:16px 18px;display:flex;flex-direction:column;gap:6px;box-shadow:var(--shadow);}
.mds-top-stat-num{font-size:28px;font-weight:800;font-family:var(--font-sans);line-height:1;}
.mds-top-stat-lbl{font-size:11px;color:var(--text-faint);font-family:var(--font-mono);}
.mds-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.mds-view-grp{display:flex;background:var(--bg-sunken);border:1px solid var(--border);border-radius:8px;padding:3px;gap:2px;}
.mds-view-btn{padding:6px 11px;border-radius:5px;border:none;cursor:pointer;background:none;color:var(--text-faint);transition:all .2s;display:flex;align-items:center;}
.mds-view-btn.active{background:var(--bg-elevated);color:var(--text);border:1px solid var(--border);box-shadow:var(--shadow);}
/* Card grid */
.mds-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:16px;}
.mds-card{border-radius:16px;border:1px solid var(--border);background:var(--bg-elevated);cursor:pointer;overflow:hidden;transition:transform .25s var(--ease),border-color .25s,box-shadow .25s;position:relative;box-shadow:var(--shadow);}
.mds-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.mds-card-glow{position:absolute;top:0;left:0;right:0;height:3px;border-radius:16px 16px 0 0;}
.mds-card-head{padding:20px 20px 14px;display:flex;align-items:flex-start;gap:14px;}
.mds-card-av{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:21px;font-weight:800;flex:none;border:1px solid;}
.mds-card-title{font-size:15.5px;font-weight:700;color:var(--text);line-height:1.25;margin-bottom:3px;}
.mds-card-table{font-size:11px;font-family:var(--font-mono);color:var(--text-faint);}
.mds-card-ns{font-size:10px;font-family:var(--font-mono);color:var(--text-faint);opacity:.6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:170px;}
.mds-card-obs{font-size:9px;padding:2px 7px;border-radius:4px;background:rgba(255,139,0,.10);color:var(--amber);border:1px solid rgba(255,139,0,.2);font-family:var(--font-mono);flex:none;}
.mds-card-sep{height:1px;background:var(--border);margin:0 20px;}
.mds-card-body{padding:14px 20px 16px;}
.mds-card-stats{display:flex;align-items:center;gap:0;margin-bottom:12px;}
.mds-card-stat-item{flex:1;text-align:center;padding:8px 0;}
.mds-card-stat-item:not(:last-child){border-right:1px solid var(--border);}
.mds-card-stat-num{font-size:18px;font-weight:800;font-family:var(--font-sans);line-height:1;}
.mds-card-stat-lbl{font-size:9.5px;font-family:var(--font-mono);color:var(--text-faint);margin-top:3px;text-transform:uppercase;letter-spacing:.06em;}
.mds-rel-bar{height:4px;border-radius:4px;display:flex;gap:1px;overflow:hidden;margin-bottom:10px;}
.mds-rel-seg{border-radius:4px;transition:flex .3s;}
.mds-rel-legend{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;}
.mds-rel-dot{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--text-faint);}
.mds-rel-dot i{width:6px;height:6px;border-radius:50%;display:inline-block;flex:none;}
.mds-trait-row{display:flex;flex-wrap:wrap;gap:5px;}
.mds-trait-pip{font-size:10px;padding:2px 8px;border-radius:5px;background:rgba(101,84,192,.08);color:var(--violet);border:1px solid rgba(101,84,192,.2);font-family:var(--font-mono);}
/* List view */
.mds-list-head{display:grid;grid-template-columns:40px 1fr 140px 60px 80px 60px;gap:10px;padding:8px 16px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-faint);font-family:var(--font-mono);margin-bottom:4px;}
.mds-list-row{display:grid;grid-template-columns:40px 1fr 140px 60px 80px 60px;align-items:center;gap:10px;padding:11px 16px;border-radius:11px;background:var(--bg-elevated);border:1px solid var(--border);margin-bottom:6px;cursor:pointer;transition:border-color .22s,transform .18s,box-shadow .18s;box-shadow:var(--shadow);}
.mds-list-row:hover{transform:translateX(4px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.mds-list-av{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex:none;border:1px solid;}
/* Detail layout */
.mds-det-wrap{display:grid;grid-template-columns:280px 1fr;gap:22px;align-items:start;}
.mds-sidebar{position:sticky;top:16px;}
.mds-side-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:var(--shadow);}
.mds-side-top{padding:26px 22px 22px;border-bottom:1px solid var(--border);text-align:center;}
.mds-side-av{width:80px;height:80px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:800;margin:0 auto 16px;border:2px solid;}
.mds-side-name{font-size:17px;font-weight:800;color:var(--text);margin-bottom:5px;word-break:break-word;}
.mds-side-tbl{font-size:11.5px;font-family:var(--font-mono);color:var(--text-faint);margin-bottom:4px;}
.mds-side-ns{font-size:10.5px;font-family:var(--font-mono);color:var(--text-faint);opacity:.65;word-break:break-all;line-height:1.5;}
.mds-side-stats{padding:14px 22px;border-bottom:1px solid var(--border);}
.mds-side-stat{display:flex;align-items:center;justify-content:space-between;padding:7px 8px;border-radius:8px;cursor:pointer;transition:background .15s;}
.mds-side-stat:hover{background:rgba(0,82,204,.05);}
.mds-side-stat-lbl{font-size:13px;color:var(--text-dim);}
.mds-side-stat-val{font-size:15px;font-weight:800;font-family:var(--font-mono);}
.mds-side-meta{padding:16px 22px;display:flex;flex-direction:column;gap:7px;}
.mds-side-chip{font-size:10.5px;padding:3px 9px;border-radius:6px;font-family:var(--font-mono);border:1px solid;display:inline-block;}
/* Main tabs */
.mds-tabs{display:flex;gap:3px;background:var(--bg-sunken);border-radius:11px;padding:4px;border:1px solid var(--border);margin-bottom:18px;}
.mds-tab-btn{flex:1;padding:9px 12px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid transparent;cursor:pointer;background:none;color:var(--text-faint);transition:all .2s;font-family:var(--font-sans);}
.mds-tab-btn.active{background:var(--bg-elevated);color:var(--text);border-color:var(--border);box-shadow:var(--shadow);}
.mds-tab-pane{display:none;}
.mds-tab-pane.active{display:block;}
.mds-section-lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-faint);font-family:var(--font-mono);margin:0 0 12px;}
/* Schema table */
.mds-schema-wrap{background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:14px;box-shadow:var(--shadow);}
.mds-schema-tbl{width:100%;border-collapse:collapse;}
.mds-schema-tbl th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-faint);font-family:var(--font-mono);padding:10px 14px;text-align:left;border-bottom:1px solid var(--border);background:var(--bg-sunken);}
.mds-schema-tbl td{padding:10px 14px;border-bottom:1px solid var(--border);vertical-align:middle;}
.mds-schema-tbl tr:last-child td{border-bottom:none;}
.mds-schema-tbl tr:hover td{background:rgba(0,82,204,.03);}
.mds-field-name{font-family:var(--font-mono);font-weight:600;font-size:13px;color:var(--text);}
.mds-fbadge{font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;font-family:var(--font-mono);border:1px solid;margin-right:4px;white-space:nowrap;display:inline-block;}
.mds-fbadge.fill{background:rgba(0,82,204,.08);color:var(--cyan);border-color:rgba(0,82,204,.2);}
.mds-fbadge.hide{background:rgba(222,53,11,.08);color:var(--rose);border-color:rgba(222,53,11,.2);}
.mds-cast-val{font-family:var(--font-mono);font-size:11px;color:var(--amber);}
/* Relationship cards */
.mds-rel-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:9px;display:flex;align-items:center;gap:14px;transition:border-color .2s,transform .15s,box-shadow .2s;box-shadow:var(--shadow);}
.mds-rel-card:hover{transform:translateX(3px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.mds-rel-method{font-family:var(--font-mono);font-size:13px;color:var(--cyan);font-weight:600;min-width:150px;}
.mds-rel-type{font-size:10px;font-weight:700;padding:3px 10px;border-radius:6px;font-family:var(--font-mono);border:1px solid;white-space:nowrap;min-width:120px;text-align:center;}
.mds-rel-arrow{font-size:14px;color:var(--text-faint);flex:none;}
.mds-rel-target{font-size:14px;font-weight:700;color:var(--text);flex:1;}
.mds-nav-btn{font-size:11px;padding:5px 12px;border-radius:7px;border:1px solid;cursor:pointer;font-family:var(--font-sans);white-space:nowrap;transition:all .2s;flex:none;}
/* Used by */
.mds-usedby-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:9px;display:flex;align-items:center;justify-content:space-between;gap:14px;transition:border-color .2s,box-shadow .2s;box-shadow:var(--shadow);}
.mds-usedby-card:hover{border-color:rgba(0,82,204,.3);box-shadow:var(--shadow-hover);}
.mds-flag-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;}
.mds-flag{font-size:11px;padding:5px 13px;border-radius:7px;font-family:var(--font-mono);font-weight:600;border:1px solid;}

/* Architecture Explorer */
.ov-panel{background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;position:relative;overflow:hidden;box-shadow:var(--shadow);}
.ov-panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:1px solid var(--border);}
.ov-panel-head h3{font-size:14.5px;font-weight:700;font-family:var(--font-sans);margin:0;color:var(--text);}
.ov-panel-head p{font-size:11.5px;color:var(--text-faint);margin:3px 0 0;}
.ov-panel-body{padding:20px 22px;}
.ov-diag-shell{overflow-x:auto;border-radius:10px;background:var(--bg-sunken);border:1px solid var(--border);}
.ov-arch-node rect{transition:filter .3s;}
.ov-arch-node:hover rect{filter:drop-shadow(0 0 10px rgba(0,82,204,.4));}
.ov-btn-icon{width:34px;height:34px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;background:var(--bg-sunken);border:1px solid var(--border);color:var(--text-dim);cursor:pointer;transition:all .25s;flex-shrink:0;font-size:16px;line-height:1;}
.ov-btn-icon:hover{background:rgba(0,82,204,.08);border-color:rgba(0,82,204,.3);color:var(--cyan);}
.ov-reveal{opacity:0;transform:translateY(14px);transition:opacity .55s var(--ease),transform .55s var(--ease);}
.ov-reveal.ov-in{opacity:1;transform:none;}
</style>
</head>
<body class="atlas-layout">

@php
$score   = $data['score'] ?? [];
$summary = $data['summary'] ?? [];
$rs      = $data['route_summary'] ?? [];
$grade   = $score['grade'] ?? 'N/A';
$gradeClass = match(strtoupper($grade[0] ?? 'F')) {
    'A' => 'grade-a', 'B' => 'grade-b', 'C' => 'grade-c', 'D' => 'grade-d', default => 'grade-f',
};
@endphp

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
        <div class="mark">
            <span class="radar"><span class="radar__ring"></span><span class="radar__ring radar__ring--delay"></span><span class="radar__sweep"></span><span class="radar__dot"></span></span>
        </div>
        <div><strong>{{ $data['project']['name'] }}</strong><span>Architecture Discovery</span></div>
    </div>

    @if(!empty($score))
    <div style="margin-bottom:20px;padding:14px 10px;background:var(--bg-hover);border-radius:10px;border:1px solid var(--border);">
        <span style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--text-faint);display:block;margin-bottom:8px;">Score</span>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span style="font-family:var(--font-mono);font-size:24px;font-weight:700;color:var(--text);">{{ $score['score'] }}<span style="font-size:13px;color:var(--text-faint);">/{{ $score['max'] }}</span></span>
            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $gradeClass }}" style="font-family:var(--font-mono);">{{ $grade }}</span>
        </div>
        <div class="atlas-score-bar"><div class="atlas-score-fill" style="width:{{ round(($score['score']/max(1,$score['max']))*100) }}%"></div></div>
    </div>
    @endif

    <nav>
        <div class="nav-group">
            <button onclick="navigate('overview')" id="nav-overview" class="nav-item nav-active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Overview
            </button>
            @if(($summary['modules']??0)>0)
            <button onclick="navigate('modules')" id="nav-modules" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Modules
                <span class="nav-badge">{{ $summary['modules'] }}</span>
            </button>
            @endif
            <button onclick="navigate('packages')" id="nav-packages" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Packages
                @if(($summary['packages']??0)>0)<span class="nav-badge">{{ $summary['packages'] }}</span>@endif
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Core</span>
            <button onclick="navigate('models')" id="nav-models" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                Models
                @if(($summary['models']??0)>0)<span class="nav-badge">{{ $summary['models'] }}</span>@endif
            </button>
            <button onclick="navigate('modelmap')" id="nav-modelmap" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Relation Graph
            </button>
            <button onclick="navigate('controllers')" id="nav-controllers" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                Controllers
                @if(($summary['controllers']??0)>0)<span class="nav-badge">{{ $summary['controllers'] }}</span>@endif
            </button>
            <button onclick="navigate('routes')" id="nav-routes" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Routes
                @if(($rs['total']??0)>0)<span class="nav-badge">{{ $rs['total'] }}</span>@endif
            </button>
            @php $apiDocCount = count($data['api_docs'] ?? []); @endphp
            <button onclick="navigate('apidocs')" id="nav-apidocs" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                API Docs
                @if($apiDocCount > 0)<span class="nav-badge">{{ $apiDocCount }}</span>@endif
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Components</span>
            <button onclick="navigate('jobs')" id="nav-jobs" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Jobs
                @if(($summary['jobs']??0)>0)<span class="nav-badge">{{ $summary['jobs'] }}</span>@endif
            </button>
            <button onclick="navigate('events')" id="nav-events" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Events
                @if(($summary['events']??0)>0)<span class="nav-badge">{{ $summary['events'] }}</span>@endif
            </button>
            <button onclick="navigate('services')" id="nav-services" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Services
                @if(($summary['services']??0)>0)<span class="nav-badge">{{ $summary['services'] }}</span>@endif
            </button>
            <button onclick="navigate('repositories')" id="nav-repositories" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Repositories
                @if(($summary['repositories']??0)>0)<span class="nav-badge">{{ $summary['repositories'] }}</span>@endif
            </button>
            <button onclick="navigate('observers')" id="nav-observers" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Observers
                @if(($summary['observers']??0)>0)<span class="nav-badge">{{ $summary['observers'] }}</span>@endif
            </button>
            <button onclick="navigate('policies')" id="nav-policies" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Policies
                @if(($summary['policies']??0)>0)<span class="nav-badge">{{ $summary['policies'] }}</span>@endif
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Architecture</span>
            <button onclick="navigate('dependencies')" id="nav-dependencies" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                Dependencies
                @if(!empty($data['dependencies']['edges']))<span class="nav-badge">{{ count($data['dependencies']['edges']) }}</span>@endif
            </button>
            <button onclick="navigate('export')" id="nav-export" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">AI</span>
            <button onclick="navigate('ai')" id="nav-ai" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                AI Insights
                @if(config('architecture-discovery.ai.enabled', false))
                <span style="margin-left:auto;width:8px;height:8px;border-radius:50%;background:var(--emerald);box-shadow:0 0 0 3px rgba(52,211,153,0.18);flex:none;"></span>
                @endif
            </button>
            <button onclick="navigate('chat')" id="nav-chat" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                AI Chat
            </button>
            <button onclick="navigate('aidocs')" id="nav-aidocs" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                AI Docs
            </button>
        </div>
    </nav>

    <div class="sidebar__scan">
        <span class="radar"><span class="radar__ring"></span><span class="radar__ring radar__ring--delay"></span><span class="radar__sweep"></span><span class="radar__dot"></span></span>
        <div>
            <strong>{{ $data['project']['name'] }}</strong>
            <span class="scan-label">v{{ $data['package_version'] }} · PHP {{ $data['php_version'] }} · {{ \Carbon\Carbon::parse($data['generated_at'])->format('M d, H:i') }}</span>
        </div>
    </div>
</aside>

{{-- ══ MAIN ══ --}}
<main class="content" style="display:flex;flex-direction:column;">

{{-- ══ TOPBAR ══ --}}
<header class="topbar">
    <div class="breadcrumb">
        <b id="topbar-section">Overview</b>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-left:auto;">
        <div class="sync-pill">
            <span class="sync-dot"></span>
            Laravel {{ $data['laravel_version'] }} · {{ $data['project']['name'] }}
        </div>
    </div>
</header>

{{-- Overview --}}
<section id="sec-overview" class="p-6">
    <div style="margin-bottom:24px;">
        <h1 class="sec-title">Overview</h1>
        <p class="sec-sub">{{ $data['project']['name'] }} · Laravel {{ $data['laravel_version'] }}</p>
    </div>

    @php
    $kpiIcons = [
        'Models'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
        'Controllers'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>',
        'Routes'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        'Jobs'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
        'Events'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
        'Services'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'Repositories' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>',
        'Observers'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        'Policies'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        'Modules'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        'Dep. Edges'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>',
    ];
    $kpiColors = [
        'Models'       => ['color'=>'var(--violet)', 'bg'=>'rgba(167,139,250,0.14)'],
        'Controllers'  => ['color'=>'var(--sky)',    'bg'=>'rgba(96,165,250,0.14)'],
        'Routes'       => ['color'=>'var(--emerald)','bg'=>'rgba(52,211,153,0.14)'],
        'Jobs'         => ['color'=>'var(--amber)',  'bg'=>'rgba(251,191,36,0.14)'],
        'Events'       => ['color'=>'var(--rose)',   'bg'=>'rgba(248,113,113,0.14)'],
        'Services'     => ['color'=>'var(--violet)', 'bg'=>'rgba(167,139,250,0.14)'],
        'Repositories' => ['color'=>'var(--cyan)',   'bg'=>'rgba(0,82,204,0.14)'],
        'Observers'    => ['color'=>'var(--amber)',  'bg'=>'rgba(251,191,36,0.14)'],
        'Policies'     => ['color'=>'var(--sky)',    'bg'=>'rgba(96,165,250,0.14)'],
        'Modules'      => ['color'=>'var(--cyan)',   'bg'=>'rgba(0,82,204,0.14)'],
        'Dep. Edges'   => ['color'=>'var(--text-dim)','bg'=>'rgba(91,103,133,0.18)'],
    ];
    $stats = [
        ['Models',       $summary['models']??0],
        ['Controllers',  $summary['controllers']??0],
        ['Routes',       $rs['total']??0],
        ['Jobs',         $summary['jobs']??0],
        ['Events',       $summary['events']??0],
        ['Services',     $summary['services']??0],
        ['Repositories', $summary['repositories']??0],
        ['Observers',    $summary['observers']??0],
        ['Policies',     $summary['policies']??0],
        ['Modules',      $summary['modules']??0],
        ['Dep. Edges',   count($data['dependencies']['edges']??[])],
    ];
    @endphp

    <div class="kpi-grid" style="margin-bottom:28px;">
        @foreach($stats as [$label,$count])
        @php $kc = $kpiColors[$label] ?? ['color'=>'var(--text-dim)','bg'=>'rgba(91,103,133,0.18)']; $ki = $kpiIcons[$label] ?? ''; @endphp
        <div class="kpi-card">
            <div class="kpi-card__icon" style="background:{{ $kc['bg'] }};color:{{ $kc['color'] }};">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">{!! $ki !!}</svg>
            </div>
            <span class="kpi-card__label">{{ $label }}</span>
            <span class="kpi-card__num" style="color:{{ $kc['color'] }};">{{ $count }}</span>
        </div>
        @endforeach
    </div>

    {{-- Architecture Explorer --}}
    <div class="ov-panel ov-reveal" data-ov-reveal style="margin-bottom:24px;">
        <div class="ov-panel-head">
            <div>
                <h3>Architecture Explorer</h3>
                <p>Request flow &mdash; from HTTP kernel to your database tables</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span id="ovArchDetail" style="font-size:11.5px;color:var(--text-faint);">Hover a node to trace it</span>
                <button class="ov-btn-icon" id="ovZoomIn" title="Zoom in">+</button>
                <button class="ov-btn-icon" id="ovZoomOut" title="Zoom out">&minus;</button>
            </div>
        </div>
        <div class="ov-panel-body" style="padding:18px 20px;">
            <div class="ov-diag-shell">
                <div id="ovArchDiagram" style="min-width:960px;"></div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
        {{-- Route breakdown --}}
        <div class="atlas-card">
            <div class="atlas-card__head"><h3>Route Breakdown</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">Total</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['total']??0 }}</span></div>
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">Web</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['web']??0 }}</span></div>
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">API</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['api']??0 }}</span></div>
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">Named</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['named_count']??0 }} / {{ $rs['total']??0 }}</span></div>
                @if(!empty($rs['api_versions']))
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">API Versions</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ implode(', ', array_keys($rs['api_versions'])) }}</span></div>
                @endif
            </div>
            @if(!empty($rs['by_method']))
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
                <p style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-faint);margin-bottom:10px;">By Method</p>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($rs['by_method'] as $method => $cnt)
                    <span class="text-xs px-2 py-0.5 rounded font-semibold method-{{ strtolower($method) }}" style="font-family:var(--font-mono);">{{ strtoupper($method) }} {{ $cnt }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Performance --}}
        <div class="atlas-card">
            <div class="atlas-card__head"><h3>Performance</h3></div>
            @php $perf = $data['performance']??[]; @endphp
            <div style="display:flex;flex-direction:column;gap:18px;">
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                        <span style="color:var(--text-faint);">Scan Time</span>
                        <span style="font-family:var(--font-mono);color:var(--cyan);">{{ $perf['execution_time_ms']??0 }} ms</span>
                    </div>
                    <div class="atlas-score-bar"><div class="atlas-score-fill" style="width:{{ min(100,($perf['execution_time_ms']??0)/50) }}%;background:linear-gradient(90deg,var(--cyan),var(--sky));"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                        <span style="color:var(--text-faint);">Memory</span>
                        <span style="font-family:var(--font-mono);color:var(--emerald);">{{ $perf['memory_usage_mb']??0 }} MB</span>
                    </div>
                    <div class="atlas-score-bar"><div class="atlas-score-fill" style="width:{{ min(100,($perf['memory_usage_mb']??0)/1.28) }}%;background:linear-gradient(90deg,var(--emerald),var(--cyan));"></div></div>
                </div>
            </div>
        </div>

        {{-- Score checks --}}
        @if(!empty($score['checks']))
        <div class="atlas-card">
            <div class="atlas-card__head"><h3>Score Checks</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($score['checks'] as $check)
                @php
                    $icColor = match($check['status']??'fail'){'pass'=>'var(--emerald)','warn'=>'var(--amber)',default=>'var(--rose)'};
                    $icSymbol = match($check['status']??'fail'){'pass'=>'✔','warn'=>'⚠',default=>'✘'};
                @endphp
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <span style="font-weight:700;font-size:13px;color:{{ $icColor }};margin-top:1px;flex:none;">{{ $icSymbol }}</span>
                    <div>
                        <p style="font-size:13px;color:var(--text);">{{ $check['label'] }}</p>
                        @if(!empty($check['note']))<p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">{{ $check['note'] }}</p>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Models --}}
<section id="sec-models" class="p-6" style="display:none">
    @php
    $mTotalRels    = collect($data['models'])->sum(fn($m) => count($m['relationships']??[]));
    $mWithObs      = collect($data['models'])->filter(fn($m) => !empty($m['observer']))->count();
    $mSoftDel      = collect($data['models'])->filter(fn($m) => collect($m['traits']??[])->contains(fn($t)=>str_contains($t,'SoftDeletes')))->count();
    $mPalette = [
        ['color'=>'var(--cyan)',    'bg'=>'rgba(0,82,204,.15)',  'border'=>'rgba(0,82,204,.3)',  'hex'=>'#0052CC'],
        ['color'=>'var(--violet)',  'bg'=>'rgba(167,139,250,.15)', 'border'=>'rgba(167,139,250,.3)', 'hex'=>'#A78BFA'],
        ['color'=>'var(--emerald)', 'bg'=>'rgba(52,211,153,.15)',  'border'=>'rgba(52,211,153,.3)',  'hex'=>'#34D399'],
        ['color'=>'var(--amber)',   'bg'=>'rgba(251,191,36,.15)',  'border'=>'rgba(251,191,36,.3)',  'hex'=>'#FBBF24'],
        ['color'=>'var(--rose)',    'bg'=>'rgba(248,113,113,.15)', 'border'=>'rgba(248,113,113,.3)', 'hex'=>'#F87171'],
        ['color'=>'var(--sky)',     'bg'=>'rgba(96,165,250,.15)',  'border'=>'rgba(96,165,250,.3)',  'hex'=>'#60A5FA'],
    ];
    $mRelColors = ['hasMany'=>'#34D399','hasOne'=>'#0052CC','belongsTo'=>'#60A5FA','belongsToMany'=>'#A78BFA','morphMany'=>'#F87171','morphTo'=>'#F87171','morphOne'=>'#F87171','hasManyThrough'=>'#FBBF24'];
    @endphp

    <div id="models-list">
        {{-- Top stats --}}
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--violet);">{{ count($data['models']) }}</span>
                <span class="mds-top-stat-lbl">Total Models</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--cyan);">{{ $mTotalRels }}</span>
                <span class="mds-top-stat-lbl">Relationships</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--amber);">{{ $mWithObs }}</span>
                <span class="mds-top-stat-lbl">With Observer</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--rose);">{{ $mSoftDel }}</span>
                <span class="mds-top-stat-lbl">Soft Deletes</span>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="mds-toolbar">
            <div class="mds-view-grp">
                <button id="mds-vbtn-grid" class="mds-view-btn active" onclick="mdsView('grid')" title="Grid">
                    <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                </button>
                <button id="mds-vbtn-list" class="mds-view-btn" onclick="mdsView('list')" title="List">
                    <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </button>
            </div>
            <input id="models-search" oninput="filterGrid('models')" type="search" placeholder="Search models…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['models']) }} models</span>
        </div>

        {{-- Grid view --}}
        <div id="mds-grid-view">
            <div class="mds-grid" id="models-grid">
                @foreach($data['models'] as $i => $model)
                @php
                    $mp      = $mPalette[$i % count($mPalette)];
                    $mRels   = $model['relationships'] ?? [];
                    $mRelCnt = count($mRels);
                    $mFillCnt= count($model['fillable'] ?? []);
                    $mTrCnt  = count($model['traits'] ?? []);
                    $mRelGrp = collect($mRels)->groupBy('type');
                    $mTotalFieldsInCard = $mRelCnt + $mFillCnt;
                @endphp
                <div class="mds-card" onclick="showDetail('models',{{ $i }})" data-name="{{ strtolower($model['name']) }}" style="--card-hover-border:{{ $mp['border'] }};">
                    <div class="mds-card-glow" style="background:linear-gradient(90deg,{{ $mp['color'] }},transparent);"></div>
                    <div class="mds-card-head">
                        <div class="mds-card-av" style="background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border-color:{{ $mp['border'] }};">{{ substr($model['name'],0,1) }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:6px;">
                                <p class="mds-card-title">{{ $model['name'] }}</p>
                                @if(!empty($model['observer']))<span class="mds-card-obs">obs</span>@endif
                            </div>
                            <p class="mds-card-table">{{ $model['table'] }}</p>
                            <p class="mds-card-ns">{{ $model['namespace'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="mds-card-sep"></div>
                    <div class="mds-card-body">
                        {{-- Stats row --}}
                        <div class="mds-card-stats" style="background:var(--bg-hover);border-radius:10px;border:1px solid var(--border);margin-bottom:12px;">
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:{{ $mp['color'] }};">{{ $mRelCnt }}</div>
                                <div class="mds-card-stat-lbl">Relations</div>
                            </div>
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:var(--cyan);">{{ $mFillCnt }}</div>
                                <div class="mds-card-stat-lbl">Fillable</div>
                            </div>
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:var(--text-dim);">{{ $mTrCnt }}</div>
                                <div class="mds-card-stat-lbl">Traits</div>
                            </div>
                        </div>

                        {{-- Relationship bar --}}
                        @if($mRelCnt > 0)
                        <div class="mds-rel-bar">
                            @foreach($mRelGrp as $rType => $rItems)
                            @php $rw = round(count($rItems) / $mRelCnt * 100); $rCol = $mRelColors[$rType] ?? '#6B778C'; @endphp
                            <div class="mds-rel-seg" style="flex:{{ $rw }};background:{{ $rCol }};opacity:.75;"></div>
                            @endforeach
                        </div>
                        <div class="mds-rel-legend">
                            @foreach($mRelGrp as $rType => $rItems)
                            @php $rCol = $mRelColors[$rType] ?? '#6B778C'; @endphp
                            <span class="mds-rel-dot"><i style="background:{{ $rCol }};"></i>{{ $rType }} ·{{ count($rItems) }}</span>
                            @endforeach
                        </div>
                        @endif

                        {{-- Traits --}}
                        @if(!empty($model['traits']))
                        <div class="mds-trait-row">
                            @foreach($model['traits'] as $tr)
                            <span class="mds-trait-pip">{{ $tr }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- List view --}}
        <div id="mds-list-view" style="display:none;">
            <div class="mds-list-head">
                <span></span><span>Model</span><span>Table</span><span>Rels</span><span>Fillable</span><span>Traits</span>
            </div>
            @foreach($data['models'] as $i => $model)
            @php $mp = $mPalette[$i % count($mPalette)]; @endphp
            <div class="mds-list-row" onclick="showDetail('models',{{ $i }})" data-name="{{ strtolower($model['name']) }}" style="border-color:var(--border);" onmouseenter="this.style.borderColor='{{ $mp['border'] }}'" onmouseleave="this.style.borderColor='var(--border)'">
                <div class="mds-list-av" style="background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border-color:{{ $mp['border'] }};">{{ substr($model['name'],0,1) }}</div>
                <div>
                    <span style="font-weight:700;font-size:13.5px;color:var(--text);">{{ $model['name'] }}</span>
                    @if(!empty($model['observer']))<span style="font-size:9px;padding:2px 6px;border-radius:4px;background:rgba(251,191,36,.12);color:var(--amber);border:1px solid rgba(251,191,36,.2);font-family:var(--font-mono);margin-left:8px;">obs</span>@endif
                </div>
                <span style="font-family:var(--font-mono);font-size:11.5px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $model['table'] }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;" style="color:{{ $mp['color'] }}">{{ count($model['relationships']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--cyan);">{{ count($model['fillable']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text-dim);">{{ count($model['traits']??[]) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Detail --}}
    <div id="models-detail" style="display:none">
        <button onclick="showList('models')" style="display:inline-flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:22px;font-family:var(--font-sans);padding:0;">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Models
        </button>
        <div id="models-detail-content"></div>
    </div>
</section>

{{-- Controllers --}}
<section id="sec-controllers" class="p-6" style="display:none">
    <div id="controllers-list">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div>
                <h1 class="sec-title">Controllers</h1>
                <p class="sec-sub">{{ count($data['controllers']) }} controllers discovered</p>
            </div>
            <input id="controllers-search" oninput="filterGrid('controllers')" type="search" placeholder="Search…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;width:180px;font-family:var(--font-mono);">
        </div>
        <div id="controllers-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($data['controllers'] as $i => $ctrl)
            <div class="ctrl-card" onclick="showDetail('controllers',{{$i}})" data-name="{{ strtolower($ctrl['name']) }}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:10px;">
                    <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                        <div class="ctrl-card__icon" style="flex:none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                        </div>
                        <div style="min-width:0;">
                            <p class="ctrl-card__name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ctrl['name'] }}</p>
                            <p class="ctrl-card__ns" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ctrl['namespace'] }}</p>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex:none;">
                        @if(!empty($ctrl['is_resource']))<span style="font-family:var(--font-mono);font-size:10px;padding:3px 8px;border-radius:12px;background:rgba(52,211,153,0.12);color:var(--emerald);border:1px solid rgba(52,211,153,0.25);">Resource</span>@endif
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:12px;">
                    <div class="ctrl-stat"><b>{{ $ctrl['method_count']??0 }}</b><span>Methods</span></div>
                    <div class="ctrl-stat"><b>{{ count($ctrl['routes']??[]) }}</b><span>Routes</span></div>
                    <div class="ctrl-stat"><b>{{ count($ctrl['dependencies']??[]) }}</b><span>Deps</span></div>
                </div>
                @if(!empty($ctrl['dependencies']))
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:10px;">
                    @foreach(array_slice($ctrl['dependencies'],0,3) as $dep)
                    <span class="ctrl-chip">{{ $dep['type'] }}</span>
                    @endforeach
                    @if(count($ctrl['dependencies'])>3)<span style="font-size:10px;color:var(--text-faint);">+{{ count($ctrl['dependencies'])-3 }}</span>@endif
                </div>
                @endif
                @if(!empty($ctrl['methods']))
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:12px;">
                    @foreach(array_slice($ctrl['methods']??[],0,4) as $m)<span class="ctrl-chip">{{ $m }}</span>@endforeach
                    @if(count($ctrl['methods']??[])>4)<span style="font-size:10px;color:var(--text-faint);">+{{ count($ctrl['methods'])-4 }} more</span>@endif
                </div>
                @endif
                <div style="border-top:1px solid var(--border);padding-top:10px;">
                    <span style="font-size:12px;color:var(--cyan);font-family:var(--font-mono);cursor:pointer;">View details →</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div id="controllers-detail" style="display:none">
        <button onclick="showList('controllers')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Controllers
        </button>
        <div id="controllers-detail-content"></div>
    </div>
</section>

{{-- Model Relationships Map --}}
<section id="sec-modelmap" class="p-6" style="display:none">

    @php
    $mmErPairs = [];
    foreach ($data['models'] as $mmModel) {
        foreach ($mmModel['relationships'] ?? [] as $mmRel) {
            $mmTo = class_basename($mmRel['related'] ?? '');
            if (!$mmTo || $mmTo === $mmModel['name']) continue;
            $mmPair = $mmModel['name'] . ':' . $mmTo;
            $mmRev  = $mmTo . ':' . $mmModel['name'];
            if (!isset($mmErPairs[$mmPair]) && !isset($mmErPairs[$mmRev])) {
                $mmType = $mmRel['type'];
                if (str_contains($mmType, 'BelongsToMany') || str_contains($mmType, 'MorphToMany')) { $mmL = '}o'; $mmR = 'o{'; }
                elseif (str_contains($mmType, 'BelongsTo') || str_contains($mmType, 'MorphTo'))     { $mmL = '}o'; $mmR = '||'; }
                elseif (str_contains($mmType, 'HasOne') || str_contains($mmType, 'MorphOne'))        { $mmL = '||'; $mmR = 'o|'; }
                else                                                                                  { $mmL = '||'; $mmR = 'o{'; }
                $mmErPairs[$mmPair] = "    {$mmModel['name']} {$mmL}--{$mmR} {$mmTo} : \"{$mmRel['method']}\"";
            }
        }
    }
    $mmMentioned = [];
    foreach (array_keys($mmErPairs) as $mmPk) { [$mmA,$mmB]=explode(':',$mmPk); $mmMentioned[$mmA]=true; $mmMentioned[$mmB]=true; }
    $mmStandalone = [];
    foreach ($data['models'] as $mmModel) {
        if (!isset($mmMentioned[$mmModel['name']])) {
            $mmStandalone[] = "    {$mmModel['name']} {"; $mmStandalone[] = "        string table \"{$mmModel['table']}\""; $mmStandalone[] = "    }";
        }
    }
    $mmErCode = "erDiagram\n".implode("\n",$mmStandalone).(!empty($mmStandalone)?"\n":'').implode("\n",$mmErPairs);

    // Per-model focused ER codes (model + its direct neighbors only)
    $mmFocused = [];
    foreach ($data['models'] as $mmFm) {
        $mmFn  = $mmFm['name'];
        $mmFps = [];
        foreach ($mmErPairs as $mmPk => $mmPl) {
            [$mmPa, $mmPb] = explode(':', $mmPk, 2);
            if ($mmPa === $mmFn || $mmPb === $mmFn) $mmFps[] = $mmPl;
        }
        $mmFocused[$mmFn] = empty($mmFps) ? '' : ("erDiagram\n" . implode("\n", $mmFps));
    }
    $mmFirstFocusModel = '';
    foreach ($data['models'] as $mmFm) {
        if (!empty($mmFm['relationships'])) { $mmFirstFocusModel = $mmFm['name']; break; }
    }
    @endphp

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;">
        <div>
            <h1 class="sec-title">Relation Graph</h1>
            <p class="sec-sub">{{ count($data['models']) }} models · {{ count($mmErPairs) }} relationships</p>
        </div>
        <div style="display:flex;background:var(--bg-hover);border-radius:8px;padding:4px;gap:3px;border:1px solid var(--border);">
            <button id="map-tab-graph" onclick="setMapTab('graph')" style="padding:6px 14px;border-radius:6px;font-size:13px;font-weight:600;background:var(--bg-elevated);color:var(--text);border:1px solid var(--border);cursor:pointer;">Relation Graph</button>
            <button id="map-tab-tree"  onclick="setMapTab('tree')"  style="padding:6px 14px;border-radius:6px;font-size:13px;font-weight:600;background:none;color:var(--text-dim);border:1px solid transparent;cursor:pointer;">Tree View</button>
            <button id="map-tab-er"    onclick="setMapTab('er')"    style="padding:6px 14px;border-radius:6px;font-size:13px;font-weight:600;background:none;color:var(--text-dim);border:1px solid transparent;cursor:pointer;">ER Diagram</button>
        </div>
    </div>

    {{-- ── TAB: Relation Graph (force-directed SVG) ── --}}
    <div id="map-graph">

        {{-- Controls row --}}
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;min-height:36px;">
            <input id="rg-search-input" type="text" placeholder="Search model…" oninput="graphSearch(this.value)"
                style="font-size:12px;font-family:var(--font-mono);background:var(--bg-elevated);border:1px solid var(--border);border-radius:9px;padding:7px 12px;color:var(--text);outline:none;width:180px;">
            <button id="rg-clear-btn" onclick="rgDiagClear()" style="display:none;font-size:11px;font-family:var(--font-mono);color:var(--text-faint);padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:transparent;cursor:pointer;">
                ✕ Clear
            </button>
            {{-- Legend --}}
            <div id="rg-legend" style="margin-left:auto;display:flex;flex-wrap:wrap;align-items:center;gap:12px 16px;">
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-faint);font-family:var(--font-mono);"><span style="display:inline-block;width:20px;height:1px;background:#818cf8;"></span>hasMany</span>
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-faint);font-family:var(--font-mono);"><span style="display:inline-block;width:20px;height:1px;background:#2dd4bf;"></span>hasOne</span>
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-faint);font-family:var(--font-mono);"><span style="display:inline-block;width:20px;height:1px;background:#34d399;"></span>belongsTo</span>
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-faint);font-family:var(--font-mono);"><span style="display:inline-block;width:20px;height:1px;background:#c084fc;"></span>M:M</span>
            </div>
            {{-- Selected node info --}}
            <div id="rg-info-row" style="display:none;margin-left:auto;align-items:center;gap:8px;">
                <span id="rg-info-name"  style="font-weight:800;color:var(--cyan);font-size:12px;font-family:var(--font-mono);"></span>
                <span id="rg-info-table" style="font-size:11px;background:rgba(0,82,204,0.12);color:var(--cyan);padding:2px 8px;border-radius:6px;font-family:var(--font-mono);"></span>
                <button id="rg-rels-btn" onclick="rgToggleRels()"
                    class="atlas-btn"
                    style="font-size:11px;padding:5px 12px;border-radius:8px;display:inline-flex;align-items:center;gap:4px;">
                    <span id="rg-info-count"></span>
                    <svg id="rg-rels-chevron" style="width:10px;height:10px;transition:transform .2s;" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4l4 4 4-4"/></svg>
                </button>
            </div>
        </div>

        {{-- Relationship cards panel --}}
        <div id="rg-rels-panel" style="display:none;margin-bottom:12px;" class="atlas-card">
            <p id="rg-rels-title" style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px;font-family:var(--font-mono);"></p>
            <div id="rg-rels-cards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px;"></div>
        </div>

        {{-- Canvas --}}
        <div style="position:relative;border-radius:16px;border:1px solid var(--border);overflow:hidden;background:var(--bg-elevated);">
            <svg id="rg-canvas" xmlns="http://www.w3.org/2000/svg"
                 style="width:100%;height:600px;display:block;cursor:grab;user-select:none">
                <defs>
                    <pattern id="rg-dot-grid" width="24" height="24" patternUnits="userSpaceOnUse">
                        <circle cx="1" cy="1" r="1" fill="rgba(23,43,77,0.12)" opacity="1"/>
                    </pattern>
                    <marker id="rg-arr-many"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#818cf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-one"       viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#2dd4bf" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-belongs"   viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#34d399" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-mm"        viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#c084fc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-many-a"    viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#0052CC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-one-a"     viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-belongs-a" viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-mm-a"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <filter id="rg-f-node"     x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="2" stdDeviation="4"  flood-color="rgba(23,43,77,0.10)"/></filter>
                    <filter id="rg-f-node-sel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="4" stdDeviation="10" flood-color="rgba(0,82,204,0.40)"/></filter>
                    <filter id="rg-f-node-rel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="3" stdDeviation="7"  flood-color="rgba(52,211,153,0.35)"/></filter>
                </defs>
                <rect width="100%" height="100%" fill="url(#rg-dot-grid)"/>
                <g id="rg-vp">
                    <g id="rg-edges-g"></g>
                    <g id="rg-nodes-g"></g>
                </g>
            </svg>

            {{-- Zoom controls --}}
            <div style="position:absolute;top:12px;right:12px;display:flex;align-items:center;gap:4px;">
                <button onclick="graphZoom(1.25)" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;color:var(--text-dim);font-weight:700;font-size:16px;cursor:pointer;" onmouseenter="this.style.background='var(--bg-hover)'" onmouseleave="this.style.background='var(--bg-elevated)'">+</button>
                <button onclick="graphZoom(0.8)"  style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;color:var(--text-dim);font-weight:700;font-size:16px;cursor:pointer;" onmouseenter="this.style.background='var(--bg-hover)'" onmouseleave="this.style.background='var(--bg-elevated)'">−</button>
                <button onclick="graphFit()"      style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;color:var(--text-dim);font-size:14px;cursor:pointer;" title="Fit to screen" onmouseenter="this.style.background='var(--bg-hover)'" onmouseleave="this.style.background='var(--bg-elevated)'">⊡</button>
                <button onclick="graphReset()"    style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;color:var(--text-dim);font-size:14px;cursor:pointer;" title="Reset" onmouseenter="this.style.background='var(--bg-hover)'" onmouseleave="this.style.background='var(--bg-elevated)'">⟳</button>
            </div>

            {{-- Minimap --}}
            <div style="position:absolute;bottom:12px;right:12px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,0.92);overflow:hidden;width:160px;height:100px;">
                <svg id="rg-minimap" width="160" height="100" style="display:block"></svg>
            </div>

            {{-- Hint --}}
            <div style="position:absolute;bottom:12px;left:12px;font-size:11px;color:var(--text-faint);background:rgba(255,255,255,0.88);padding:4px 10px;border-radius:8px;border:1px solid var(--border);pointer-events:none;">
                Click node · Drag to pan · Scroll to zoom
            </div>
        </div>

    </div>

    {{-- ── TAB: Tree View ── --}}
    <div id="map-tree" style="display:none">
        <div style="margin-bottom:16px;">
            <input id="map-search" oninput="filterModelTree()" type="search" placeholder="Filter models…"
                style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;padding:8px 14px;font-size:13px;color:var(--text);font-family:var(--font-sans);width:220px;outline:none;">
        </div>
        <div id="map-tree-content" style="display:flex;flex-direction:column;gap:10px;"></div>
    </div>

    {{-- ── TAB: ER Diagram ── --}}
    <div id="map-er" style="display:none">
        @if(empty($mmErPairs) && empty($mmStandalone))
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:48px;text-align:center;">
            <p style="color:var(--text-faint);font-size:13px;">No relationships found across models.</p>
        </div>
        @else

        {{-- Toolbar --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;">

            {{-- Focus model selector --}}
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">Focus:</span>
                <select id="er-focus-select" onchange="erFocus(this.value)"
                    style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:9px;padding:7px 28px 7px 12px;font-size:12px;color:var(--text);font-family:var(--font-mono);cursor:pointer;outline:none;">
                    <option value="__all__">All Models</option>
                    @foreach($data['models'] as $erM)
                    <option value="{{ $erM['name'] }}">{{ $erM['name'] }}{{ count($erM['relationships']??[]) ? ' ('.count($erM['relationships']).' rels)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Stats --}}
            <span style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['models']) }} models · {{ count($mmErPairs) }} relationships</span>

            {{-- Large-project warning --}}
            @if(count($data['models']) > 20)
            <div style="display:flex;align-items:center;gap:7px;padding:6px 12px;background:rgba(251,191,36,.07);border:1px solid rgba(251,191,36,.2);border-radius:8px;font-size:11px;color:var(--amber);margin-left:auto;">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:13px;height:13px;flex:none;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Large project — auto-focused on a single model. Select "All Models" to see everything.
            </div>
            @endif
        </div>

        {{-- Diagram container --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:24px;overflow:auto;min-height:280px;">
            <div class="mermaid" id="er-diagram">{!! $mmErCode !!}</div>
        </div>
        @endif
    </div>

</section>

{{-- Routes --}}
<section id="sec-routes" class="p-6" style="display:none">

    @php
    $routeMethodCounts = [];
    foreach ($data['routes'] as $r) {
        foreach (array_filter($r['methods']??[], fn($m)=>$m!=='HEAD') as $m) {
            $routeMethodCounts[strtoupper($m)] = ($routeMethodCounts[strtoupper($m)] ?? 0) + 1;
        }
    }
    $routeMethodStyle = [
        'GET'    => 'bg-emerald-500 text-white',
        'POST'   => 'bg-blue-500 text-white',
        'PUT'    => 'bg-amber-500 text-white',
        'PATCH'  => 'bg-orange-500 text-white',
        'DELETE' => 'bg-red-500 text-white',
    ];
    @endphp

    {{-- List view --}}
    <div id="routes-list">

        {{-- Header --}}
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;">
            <div>
                <h1 class="sec-title">Route Explorer</h1>
                <p class="sec-sub">{{ $rs['total']??0 }} routes · click any row to explore its full pipeline</p>
            </div>
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;">
                <div style="position:relative;">
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="routes-search" oninput="filterRoutes()" type="search" placeholder="Search URI or handler…"
                        style="border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 32px;font-size:12px;width:220px;font-family:var(--font-mono);">
                </div>
                <select id="routes-method-filter" onchange="filterRoutes()"
                    style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;font-family:var(--font-mono);">
                    <option value="">All Methods</option>
                    @foreach(array_keys($rs['by_method']??[]) as $m)<option value="{{ strtoupper($m) }}">{{ strtoupper($m) }}</option>@endforeach
                </select>
                <select id="routes-mw-filter" onchange="filterRoutes()"
                    style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;font-family:var(--font-mono);max-width:180px;">
                    <option value="">All Middleware</option>
                    @foreach(array_keys($rs['middleware_usage']??[]) as $mw)
                    <option value="{{ $mw }}">{{ class_basename($mw) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Method stats pills --}}
        @if(!empty($routeMethodCounts))
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;">
            @foreach($routeMethodCounts as $method => $cnt)
            <button onclick="document.getElementById('routes-method-filter').value='{{ $method }}'; filterRoutes();"
                style="display:flex;align-items:center;gap:8px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;padding:7px 14px;cursor:pointer;transition:border-color .2s;">
                <span class="text-xs font-bold px-2 py-0.5 rounded-md method-{{ strtolower($method) }}" style="font-family:var(--font-mono);">{{ $method }}</span>
                <span style="font-size:14px;font-weight:700;font-family:var(--font-mono);color:var(--text);">{{ $cnt }}</span>
            </button>
            @endforeach
            <button onclick="document.getElementById('routes-method-filter').value=''; filterRoutes();"
                style="display:flex;align-items:center;gap:8px;background:var(--bg-hover);border:1px solid var(--border-strong);border-radius:10px;padding:7px 14px;cursor:pointer;">
                <span style="font-size:11px;font-weight:700;font-family:var(--font-mono);color:var(--text-faint);">ALL</span>
                <span style="font-size:14px;font-weight:700;font-family:var(--font-mono);color:var(--text);">{{ $rs['total']??0 }}</span>
            </button>
        </div>
        @endif

        {{-- Table --}}
        <div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <table class="w-full" style="border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-hover);border-bottom:1px solid var(--border);">
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:100px;">Method</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;">URI</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:200px;">Handler</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:160px;">Middleware</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:200px;">Route Name</th>
                        <th style="width:32px;"></th>
                    </tr>
                </thead>
                <tbody id="routes-tbody">
                    @foreach($data['routes'] as $i => $route)
                    @php
                        $methods     = array_values(array_filter($route['methods']??[], fn($m)=>$m!=='HEAD'));
                        $ctrl        = class_basename($route['controller']['class']??'');
                        $action      = $route['controller']['method']??'Closure';
                        $isInvokable = $action === '__invoke';
                        $mwsRaw      = implode(',', array_map('strtolower', $route['middleware']??[]));
                        $allMws      = $route['middleware'] ?? [];
                        $mwCount     = count($allMws);
                        $primaryMw   = $mwCount > 0 ? class_basename($allMws[0]) : null;
                        $fullMwTitle = implode(' · ', $allMws);
                        $routeName   = $route['name'] ?? '';
                    @endphp
                    <tr class="route-row" style="border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s;"
                        onclick="showRouteDetail({{ $i }})"
                        data-uri="{{ strtolower($route['uri']??'') }}"
                        data-methods="{{ implode(',',array_map('strtoupper',$methods)) }}"
                        data-mw="{{ $mwsRaw }}">

                        {{-- Method --}}
                        <td style="padding:10px 16px;">
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                @foreach($methods as $m)
                                <span class="text-xs px-2 py-0.5 rounded-md font-bold method-{{ strtolower($m) }}" style="font-family:var(--font-mono);">{{ $m }}</span>
                                @endforeach
                            </div>
                        </td>

                        {{-- URI --}}
                        <td style="padding:10px 16px;">
                            <code style="font-size:12px;font-family:var(--font-mono);color:var(--text);">{{ $route['uri'] }}</code>
                        </td>

                        {{-- Handler --}}
                        <td style="padding:10px 16px;font-size:12px;">
                            @if($ctrl)
                                <span style="font-weight:700;color:var(--text);font-family:var(--font-mono);">{{ $ctrl }}</span>
                                @if(!$isInvokable)
                                    <span style="color:var(--border-strong);margin:0 2px;">@</span><span style="color:var(--text-faint);font-family:var(--font-mono);">{{ $action }}</span>
                                @endif
                            @else
                                <span style="color:var(--text-faint);font-style:italic;">Closure</span>
                            @endif
                        </td>

                        {{-- Middleware — compact single line --}}
                        <td style="padding:10px 16px;">
                            @if($mwCount === 0)
                                <span style="font-size:12px;color:var(--text-faint);">—</span>
                            @else
                                <div style="display:flex;align-items:center;gap:6px;" title="{{ $fullMwTitle }}">
                                    <span class="ctrl-chip" style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $allMws[0] }}">{{ $primaryMw }}</span>
                                    @if($mwCount > 1)
                                    <span class="ctrl-chip" style="white-space:nowrap;flex:none;" title="{{ $fullMwTitle }}">+{{ $mwCount - 1 }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Route name --}}
                        <td style="padding:10px 16px;font-family:var(--font-mono);font-size:12px;color:var(--text-faint);">
                            @if($routeName)
                                <span style="display:block;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $routeName }}">{{ $routeName }}</span>
                            @else
                                <span style="color:var(--border-strong);">—</span>
                            @endif
                        </td>

                        {{-- Chevron --}}
                        <td style="padding:10px 16px 10px 0;text-align:right;">
                            <svg style="width:16px;height:16px;color:var(--text-faint);display:inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail view --}}
    <div id="routes-detail" style="display:none">
        <button onclick="showList('routes')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Route Explorer
        </button>
        <div id="routes-detail-content"></div>
    </div>

</section>

{{-- ══ API DOCUMENTATION ══ --}}
<section id="sec-apidocs" style="display:none;padding:24px;">

    @php
    $apiDocs   = $data['api_docs'] ?? [];
    $apiGroups = [];
    foreach ($apiDocs as $ep) {
        $apiGroups[$ep['group']][] = $ep;
    }
    ksort($apiGroups);

    $methodStyle = [
        'GET'    => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.15)', 'border'=>'rgba(52,211,153,.3)', 'left'=>'#34D399'],
        'POST'   => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.15)', 'border'=>'rgba(96,165,250,.3)', 'left'=>'#60A5FA'],
        'PUT'    => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.15)', 'border'=>'rgba(251,191,36,.3)', 'left'=>'#FBBF24'],
        'PATCH'  => ['color'=>'#FB923C','bg'=>'rgba(251,146,60,.15)', 'border'=>'rgba(251,146,60,.3)', 'left'=>'#FB923C'],
        'DELETE' => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.15)','border'=>'rgba(248,113,113,.3)','left'=>'#F87171'],
    ];
    $defaultStyle = ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)','border'=>'rgba(142,155,184,.25)','left'=>'#6B778C'];

    $statusStyle = fn(int $code) => match(true) {
        $code < 300 => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.12)','border'=>'rgba(52,211,153,.25)'],
        $code < 500 => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.12)','border'=>'rgba(248,113,113,.25)'],
        default     => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.12)','border'=>'rgba(251,191,36,.25)'],
    };

    $typeStyle = [
        'string'  => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)'],
        'integer' => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)'],
        'number'  => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)'],
        'boolean' => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.12)'],
        'array'   => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.12)'],
        'file'    => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.12)'],
        'email'   => ['color'=>'#0052CC','bg'=>'rgba(0,82,204,.12)'],
        'url'     => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)'],
        'date'    => ['color'=>'#FB923C','bg'=>'rgba(251,146,60,.12)'],
        'uuid'    => ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)'],
        'enum'    => ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.12)'],
    ];
    $defaultType = ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)'];

    $groupLabel = fn(string $g) => ucwords(str_replace(['-', '_'], ' ', $g));

    $methodCounts = array_fill_keys(['GET','POST','PUT','PATCH','DELETE'], 0);
    foreach ($apiDocs as $ep) {
        $m = strtoupper($ep['method'] ?? '');
        if (isset($methodCounts[$m])) $methodCounts[$m]++;
    }
    @endphp

    {{-- Page header --}}
    <div style="margin-bottom:24px;">
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px;">
            <div>
                <h1 class="sec-title">API Documentation</h1>
                <p class="sec-sub">
                    {{ count($apiDocs) }} endpoint{{ count($apiDocs) !== 1 ? 's' : '' }} across
                    {{ count($apiGroups) }} resource{{ count($apiGroups) !== 1 ? 's' : '' }} · auto-generated from routes
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                {{-- Search input --}}
                <div style="position:relative;">
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="api-search" type="text" placeholder="Search endpoints…" oninput="apiSearch(this.value)"
                        style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 32px;font-size:12px;width:220px;font-family:var(--font-mono);color:var(--text);outline:none;">
                </div>
                {{-- Method filter pill group --}}
                <div id="api-method-filters"
                     style="display:flex;gap:0;background:var(--bg-elevated);border:1px solid var(--border);border-radius:9px;padding:3px;">
                    @foreach(['ALL','GET','POST','PUT','PATCH','DELETE'] as $mf)
                    @php
                        $mfStyle = $methodStyle[$mf] ?? null;
                        $isAll   = $mf === 'ALL';
                    @endphp
                    <button onclick="apiFilter('{{ $mf }}')" data-method="{{ $mf }}"
                        class="api-filter-btn"
                        style="font-size:10px;padding:5px 10px;border-radius:6px;border:none;font-weight:800;font-family:var(--font-mono);cursor:pointer;transition:background .15s,color .15s;{{ $isAll ? 'background:var(--bg-hover);color:var(--text);' : 'background:transparent;color:var(--text-faint);' }}">
                        {{ $mf }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Method stats row --}}
        @if(!empty($apiDocs))
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;">
            @foreach($methodCounts as $method => $cnt)
            @if($cnt > 0)
            @php $ms = $methodStyle[$method] ?? $defaultStyle; @endphp
            <div style="display:flex;align-items:center;gap:10px;background:var(--bg-elevated);border:1px solid var(--border);border-left:3px solid {{ $ms['left'] }};border-radius:10px;padding:8px 16px;">
                <span style="font-family:var(--font-mono);font-size:10px;font-weight:800;padding:3px 8px;border-radius:5px;background:{{ $ms['bg'] }};color:{{ $ms['color'] }};border:1px solid {{ $ms['border'] }};">{{ $method }}</span>
                <span style="font-size:18px;font-weight:800;font-family:var(--font-mono);color:var(--text);line-height:1;">{{ $cnt }}</span>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>

    @if(empty($apiDocs))
    {{-- Empty state --}}
    <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;padding:64px 24px;text-align:center;">
        <div style="width:56px;height:56px;background:rgba(0,82,204,.08);border:1px solid rgba(0,82,204,.2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:26px;height:26px;color:#0052CC;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p style="font-size:14px;font-weight:700;color:var(--text);margin:0 0 8px;">No API routes found</p>
        <p style="font-size:12px;color:var(--text-faint);margin:0;">
            Define routes under the
            <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);color:var(--cyan);">api/</code>
            prefix or apply the
            <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);color:var(--cyan);">api</code>
            middleware group.
        </p>
    </div>
    @else

    {{-- Resource tab strip --}}
    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;padding:4px 0;margin-bottom:18px;border-bottom:1px solid var(--border);padding-bottom:14px;">
        @foreach($apiGroups as $groupName => $endpoints)
        <button onclick="apiScrollTo('{{ $groupName }}')" class="api-nav-item"
            data-group-tab="{{ $groupName }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-elevated);color:var(--text-dim);font-size:12px;font-weight:600;cursor:pointer;transition:all .18s;white-space:nowrap;">
            <span>{{ $groupLabel($groupName) }}</span>
            <span style="font-family:var(--font-mono);font-size:10px;background:var(--bg-hover);color:var(--text-faint);padding:1px 6px;border-radius:6px;">{{ count($endpoints) }}</span>
        </button>
        @endforeach
    </div>

    {{-- Full-width endpoint groups --}}
    <div id="api-groups-container" style="display:flex;flex-direction:column;gap:20px;">
        @foreach($apiGroups as $groupName => $endpoints)
        <div class="api-group" id="api-group-{{ $groupName }}" data-group="{{ $groupName }}">

            {{-- Group header --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                <div style="width:32px;height:32px;background:rgba(0,82,204,.1);border:1px solid rgba(0,82,204,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:16px;height:16px;color:#0052CC;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div style="min-width:0;">
                    <h3 style="font-size:14px;font-weight:700;color:var(--text);margin:0;line-height:1.3;">{{ $groupLabel($groupName) }}</h3>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);margin:2px 0 0;">/{{ $groupName }}</p>
                </div>
                <span style="background:var(--bg-elevated);border:1px solid var(--border);color:var(--text-dim);font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;flex-shrink:0;margin-left:auto;">
                    {{ count($endpoints) }} endpoint{{ count($endpoints) !== 1 ? 's' : '' }}
                </span>
            </div>

            {{-- Endpoint cards container --}}
            <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
            @foreach($endpoints as $epIdx => $ep)
            @php
                $ms      = $methodStyle[$ep['method']] ?? $defaultStyle;
                $hasBody = !empty($ep['body_params']);
                $hasPath = !empty($ep['path_params']);
                $uid     = $groupName . '_' . $epIdx;
                $handler = $ep['controller'] . '@' . $ep['action'];
                $isLast  = $epIdx === array_key_last($endpoints);
            @endphp
            <div class="api-endpoint-wrap"
                 data-method="{{ $ep['method'] }}" data-uri="{{ strtolower($ep['uri']) }}"
                 style="{{ $isLast ? '' : 'border-bottom:1px solid var(--border);' }}">

                {{-- Collapsed row --}}
                <div onclick="apiToggle('{{ $uid }}')"
                     style="display:flex;align-items:center;gap:12px;padding:12px 16px;cursor:pointer;border-left:3px solid {{ $ms['left'] }};transition:background .18s;user-select:none;"
                     onmouseenter="this.style.background='var(--bg-hover)'" onmouseleave="this.style.background='transparent'">

                    {{-- Method badge --}}
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:800;width:60px;text-align:center;padding:4px 0;border-radius:6px;background:{{ $ms['bg'] }};color:{{ $ms['color'] }};border:1px solid {{ $ms['border'] }};flex-shrink:0;">
                        {{ $ep['method'] }}
                    </span>

                    {{-- URI + route name --}}
                    <div style="flex:1;min-width:0;">
                        <code style="font-family:var(--font-mono);font-size:13px;color:var(--text);font-weight:600;">{{ $ep['uri'] }}</code>
                        @if($ep['name'])
                        <p style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin:2px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ep['name'] }}</p>
                        @endif
                    </div>

                    {{-- Badges + handler + chevron --}}
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        @if($ep['auth_required'])
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:#FBBF24;background:rgba(251,191,36,.12);padding:3px 8px;border-radius:20px;border:1px solid rgba(251,191,36,.3);">
                            <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Auth
                        </span>
                        @endif
                        @if($hasBody)
                        <span style="font-size:10px;font-weight:600;color:#60A5FA;background:rgba(96,165,250,.12);padding:3px 8px;border-radius:20px;border:1px solid rgba(96,165,250,.3);">Body</span>
                        @endif
                        @if($hasPath)
                        <span style="font-size:10px;font-weight:600;color:#34D399;background:rgba(52,211,153,.12);padding:3px 8px;border-radius:20px;border:1px solid rgba(52,211,153,.3);">Params</span>
                        @endif
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $handler }}">{{ $handler }}</span>
                        <svg id="chevron-{{ $uid }}" style="width:14px;height:14px;color:var(--text-faint);flex-shrink:0;transition:transform .2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Expanded detail panel --}}
                <div id="detail-{{ $uid }}" style="display:none;border-top:1px solid var(--border);">

                    {{-- Detail header bar --}}
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--bg-sunken);">
                        <span style="font-family:var(--font-mono);font-size:10px;font-weight:800;padding:3px 8px;border-radius:5px;background:{{ $ms['bg'] }};color:{{ $ms['color'] }};border:1px solid {{ $ms['border'] }};flex-shrink:0;">{{ $ep['method'] }}</span>
                        <code style="font-family:var(--font-mono);font-size:12px;font-weight:600;color:var(--text);">{{ $ep['uri'] }}</code>
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin-left:auto;">{{ $handler }}</span>
                    </div>

                    {{-- Detail body --}}
                    <div style="background:var(--bg-hover);padding:18px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                            {{-- Left column: parameters --}}
                            <div style="display:flex;flex-direction:column;gap:18px;">

                                {{-- Path parameters --}}
                                @if($hasPath)
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:#34D399;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Path Parameters</p>
                                    </div>
                                    <div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                                        <table style="width:100%;border-collapse:collapse;font-size:11px;">
                                            <thead>
                                                <tr style="background:rgba(255,255,255,.03);border-bottom:1px solid var(--border);">
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Name</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Type</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Required</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($ep['path_params'] as $ppIdx => $pp)
                                            @php $ts = $typeStyle[$pp['type']] ?? $defaultType; @endphp
                                            <tr style="{{ $ppIdx > 0 ? 'border-top:1px solid var(--border);' : '' }}">
                                                <td style="padding:9px 12px;font-family:var(--font-mono);font-weight:700;color:var(--text);font-size:12px;">{{ $pp['name'] }}</td>
                                                <td style="padding:9px 12px;">
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 7px;border-radius:4px;background:{{ $ts['bg'] }};color:{{ $ts['color'] }};">{{ $pp['type'] }}</span>
                                                </td>
                                                <td style="padding:9px 12px;">
                                                    @if($pp['required'])
                                                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:rgba(248,113,113,.12);color:#F87171;border:1px solid rgba(248,113,113,.25);">required</span>
                                                    @else
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 8px;border-radius:10px;background:var(--bg-hover);color:var(--text-faint);">optional</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                {{-- Body parameters --}}
                                @if($hasBody)
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                                        <span style="width:3px;height:16px;background:#60A5FA;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Request Body</p>
                                        @if($ep['request_class'])
                                        <span style="font-family:var(--font-mono);font-size:10px;color:#60A5FA;background:rgba(96,165,250,.12);padding:2px 8px;border-radius:10px;border:1px solid rgba(96,165,250,.25);">{{ $ep['request_class'] }}</span>
                                        @endif
                                    </div>
                                    <div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                                        <table style="width:100%;border-collapse:collapse;font-size:11px;">
                                            <thead>
                                                <tr style="background:rgba(255,255,255,.03);border-bottom:1px solid var(--border);">
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Field</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Type</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Required</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Rules</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($ep['body_params'] as $bpIdx => $bp)
                                            @php $ts = $typeStyle[$bp['type']] ?? $defaultType; @endphp
                                            <tr style="{{ $bpIdx > 0 ? 'border-top:1px solid var(--border);' : '' }}">
                                                <td style="padding:9px 12px;font-family:var(--font-mono);font-weight:700;color:var(--text);font-size:12px;">{{ $bp['field'] }}</td>
                                                <td style="padding:9px 12px;">
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 7px;border-radius:4px;background:{{ $ts['bg'] }};color:{{ $ts['color'] }};">{{ $bp['type'] }}</span>
                                                </td>
                                                <td style="padding:9px 12px;">
                                                    @if($bp['required'])
                                                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:rgba(248,113,113,.12);color:#F87171;border:1px solid rgba(248,113,113,.25);">required</span>
                                                    @elseif($bp['nullable'])
                                                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:rgba(251,191,36,.12);color:#FBBF24;border:1px solid rgba(251,191,36,.25);">nullable</span>
                                                    @else
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 8px;border-radius:10px;background:var(--bg-hover);color:var(--text-faint);">optional</span>
                                                    @endif
                                                </td>
                                                <td style="padding:9px 12px;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);max-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $bp['rules'] }}">{{ $bp['rules'] }}</td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @if(!$hasPath && !$hasBody)
                                <div style="display:flex;align-items:center;gap:12px;padding:16px;background:var(--bg-elevated);border:1px dashed var(--border);border-radius:10px;">
                                    <svg style="width:18px;height:18px;color:var(--text-faint);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p style="font-size:11px;color:var(--text-faint);margin:0;">No parameters detected for this endpoint.</p>
                                </div>
                                @endif
                            </div>

                            {{-- Right column: responses + middleware + route name --}}
                            <div style="display:flex;flex-direction:column;gap:18px;">

                                {{-- Responses --}}
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:#34D399;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Responses</p>
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:6px;">
                                        @foreach($ep['responses'] as $code => $label)
                                        @php $sc = $statusStyle((int)$code); @endphp
                                        <div style="display:flex;align-items:center;gap:10px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;padding:8px 12px;">
                                            <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};flex-shrink:0;">{{ $code }}</span>
                                            <span style="font-size:12px;color:var(--text-dim);">{{ $label }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Middleware --}}
                                @if(!empty($ep['middleware']))
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:var(--text-faint);border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Middleware</p>
                                    </div>
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                        @foreach($ep['middleware'] as $mw)
                                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-dim);background:var(--bg-elevated);border:1px solid var(--border);padding:4px 10px;border-radius:8px;">{{ $mw }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                {{-- Route name --}}
                                @if($ep['name'])
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:#A78BFA;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Route Name</p>
                                    </div>
                                    <code style="font-family:var(--font-mono);font-size:11px;color:#A78BFA;background:rgba(167,139,250,.1);border:1px solid rgba(167,139,250,.25);padding:6px 12px;border-radius:8px;display:inline-block;">{{ $ep['name'] }}</code>
                                </div>
                                @endif

                            </div>{{-- /right column --}}
                        </div>{{-- /grid --}}
                    </div>{{-- /detail body --}}

                    {{-- Request Flow graph --}}
                    <div id="api-flow-{{ $uid }}"
                         style="padding:0 16px 16px;background:var(--bg-hover);"
                         data-controller="{{ $ep['controller'] }}"
                         data-action="{{ $ep['action'] }}"
                         data-method="{{ $ep['method'] }}"
                         data-uri="{{ $ep['uri'] }}"
                         data-rname="{{ $ep['name'] ?? '' }}"
                         data-mws="{{ json_encode($ep['middleware'] ?? []) }}">
                    </div>

                </div>{{-- /expanded panel --}}

            </div>{{-- /api-endpoint-wrap --}}
            @endforeach
            </div>{{-- /endpoint cards container --}}
        </div>{{-- /api-group --}}
        @endforeach
    </div>{{-- /api-groups-container --}}
    @endif

</section>

{{-- Jobs --}}
<section id="sec-jobs" class="p-6" style="display:none">
    <div id="jobs-list">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div><h1 class="sec-title">Jobs</h1><p class="sec-sub">{{ count($data['jobs']) }} queued jobs</p></div>
            <input id="jobs-search" oninput="filterGrid('jobs')" type="search" placeholder="Search…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;width:180px;font-family:var(--font-mono);">
        </div>
        @if(empty($data['jobs']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No jobs found in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Jobs</code></p></div>
        @else
        <div id="jobs-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($data['jobs'] as $i => $job)
            <div class="card" style="padding:18px;cursor:pointer;" onclick="showDetail('jobs',{{$i}})" data-name="{{ strtolower($job['name']) }}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;">
                    <p style="font-weight:700;font-size:15px;color:var(--text);">{{ $job['name'] }}</p>
                    @if($job['queued']??false)<span style="font-family:var(--font-mono);font-size:10px;background:rgba(251,191,36,0.12);color:var(--amber);padding:3px 8px;border-radius:12px;border:1px solid rgba(251,191,36,0.25);">Queued</span>@endif
                </div>
                <p style="font-size:12px;color:var(--text-faint);margin-bottom:8px;">Queue: <span style="font-family:var(--font-mono);color:var(--text);">{{ $job['queue']??'default' }}</span></p>
                <div style="display:flex;gap:12px;font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">
                    @if($job['tries']??null)<span>Tries: {{ $job['tries'] }}</span>@endif
                    @if($job['timeout']??null)<span>Timeout: {{ $job['timeout'] }}s</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="jobs-detail" style="display:none">
        <button onclick="showList('jobs')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Jobs
        </button>
        <div id="jobs-detail-content"></div>
    </div>
</section>

{{-- Events --}}
<section id="sec-events" class="p-6" style="display:none">
    <div id="events-list">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div><h1 class="sec-title">Events</h1><p class="sec-sub">{{ count($data['events']) }} events</p></div>
            <input id="events-search" oninput="filterGrid('events')" type="search" placeholder="Search…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;width:180px;font-family:var(--font-mono);">
        </div>
        @if(empty($data['events']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No events found in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Events</code></p></div>
        @else
        <div id="events-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($data['events'] as $i => $evt)
            <div class="card" style="padding:18px;cursor:pointer;" onclick="showDetail('events',{{$i}})" data-name="{{ strtolower($evt['name']) }}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;">
                    <p style="font-weight:700;font-size:15px;color:var(--text);">{{ $evt['name'] }}</p>
                    @if($evt['broadcasts']??false)<span style="font-family:var(--font-mono);font-size:10px;background:rgba(248,113,113,0.12);color:var(--rose);padding:3px 8px;border-radius:12px;border:1px solid rgba(248,113,113,0.25);">Broadcast</span>@endif
                </div>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $evt['namespace'] }}</p>
                @if(!empty($evt['properties']))<p style="font-size:12px;color:var(--text-dim);margin-top:8px;">{{ count($evt['properties']) }} payload props</p>@endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="events-detail" style="display:none">
        <button onclick="showList('events')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Events
        </button>
        <div id="events-detail-content"></div>
    </div>
</section>

{{-- Services --}}
<section id="sec-services" class="p-6" style="display:none">
    <div id="services-list">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div><h1 class="sec-title">Services</h1><p class="sec-sub">{{ count($data['services']) }} service classes</p></div>
            <input id="services-search" oninput="filterGrid('services')" type="search" placeholder="Search…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;width:180px;font-family:var(--font-mono);">
        </div>
        @if(empty($data['services']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No services found in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Services</code></p></div>
        @else
        <div id="services-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($data['services'] as $i => $svc)
            <div class="card" style="padding:18px;cursor:pointer;" onclick="showDetail('services',{{$i}})" data-name="{{ strtolower($svc['name']) }}">
                <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px;">{{ $svc['name'] }}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:10px;">{{ $svc['namespace'] }}</p>
                <div style="display:flex;gap:12px;font-size:12px;color:var(--text-dim);">
                    <span>{{ count($svc['methods']??[]) }} methods</span>
                    @if(!empty($svc['dependencies']))<span>{{ count($svc['dependencies']) }} deps</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="services-detail" style="display:none">
        <button onclick="showList('services')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Services
        </button>
        <div id="services-detail-content"></div>
    </div>
</section>

{{-- Repositories --}}
<section id="sec-repositories" class="p-6" style="display:none">
    <div id="repositories-list">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div><h1 class="sec-title">Repositories</h1><p class="sec-sub">{{ count($data['repositories']) }} repositories</p></div>
            <input id="repositories-search" oninput="filterGrid('repositories')" type="search" placeholder="Search…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;width:180px;font-family:var(--font-mono);">
        </div>
        @if(empty($data['repositories']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No repositories found in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Repositories</code></p></div>
        @else
        <div id="repositories-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($data['repositories'] as $i => $repo)
            <div class="card" style="padding:18px;cursor:pointer;" onclick="showDetail('repositories',{{$i}})" data-name="{{ strtolower($repo['name']) }}">
                <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px;">{{ $repo['name'] }}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:10px;">{{ $repo['namespace'] }}</p>
                <div style="display:flex;gap:12px;font-size:12px;color:var(--text-dim);">
                    <span>{{ count($repo['methods']??[]) }} methods</span>
                    @if(!empty($repo['dependencies']))<span>{{ count($repo['dependencies']) }} deps</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="repositories-detail" style="display:none">
        <button onclick="showList('repositories')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Repositories
        </button>
        <div id="repositories-detail-content"></div>
    </div>
</section>

{{-- Observers --}}
<section id="sec-observers" class="p-6" style="display:none">
    <div id="observers-list">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div><h1 class="sec-title">Observers</h1><p class="sec-sub">{{ count($data['observers']) }} observers</p></div>
            <input id="observers-search" oninput="filterGrid('observers')" type="search" placeholder="Search…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;width:180px;font-family:var(--font-mono);">
        </div>
        @if(empty($data['observers']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No observers found in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Observers</code></p></div>
        @else
        <div id="observers-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($data['observers'] as $i => $obs)
            <div class="card" style="padding:18px;cursor:pointer;" onclick="showDetail('observers',{{$i}})" data-name="{{ strtolower($obs['name']) }}">
                <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">{{ $obs['name'] }}</p>
                <p style="font-size:12px;color:var(--text-dim);margin-bottom:10px;">Observes: <span style="color:var(--text);font-family:var(--font-mono);">{{ $obs['observes']??'Unknown' }}</span></p>
                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                    @foreach($obs['events']??[] as $e)<span style="font-size:10px;padding:3px 7px;border-radius:5px;background:rgba(251,191,36,0.12);color:var(--amber);border:1px solid rgba(251,191,36,0.2);">{{ $e }}</span>@endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="observers-detail" style="display:none">
        <button onclick="showList('observers')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Observers
        </button>
        <div id="observers-detail-content"></div>
    </div>
</section>

{{-- Policies --}}
<section id="sec-policies" class="p-6" style="display:none">
    <div id="policies-list">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div><h1 class="sec-title">Policies</h1><p class="sec-sub">{{ count($data['policies']) }} policies</p></div>
            <input id="policies-search" oninput="filterGrid('policies')" type="search" placeholder="Search…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;width:180px;font-family:var(--font-mono);">
        </div>
        @if(empty($data['policies']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No policies found in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Policies</code></p></div>
        @else
        <div id="policies-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($data['policies'] as $i => $pol)
            <div class="card" style="padding:18px;cursor:pointer;" onclick="showDetail('policies',{{$i}})" data-name="{{ strtolower($pol['name']) }}">
                <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">{{ $pol['name'] }}</p>
                <p style="font-size:12px;color:var(--text-dim);margin-bottom:10px;">Guards: <span style="color:var(--text);font-family:var(--font-mono);">{{ $pol['model']??'Unknown' }}</span></p>
                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                    @foreach($pol['actions']??[] as $a)<span class="ctrl-chip">{{ $a }}</span>@endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="policies-detail" style="display:none">
        <button onclick="showList('policies')" style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--cyan);background:none;border:none;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Policies
        </button>
        <div id="policies-detail-content"></div>
    </div>
</section>

{{-- Dependencies --}}
<section id="sec-dependencies" class="p-6" style="display:none">
    <h1 class="sec-title" style="margin-bottom:6px;">Dependency Graph</h1>
    <p class="sec-sub" style="margin-bottom:24px;">{{ count($data['dependencies']['nodes']??[]) }} nodes · {{ count($data['dependencies']['edges']??[]) }} edges — how your classes connect across layers</p>

    @php
    $depNodes = $data['dependencies']['nodes'] ?? [];
    $depEdges = $data['dependencies']['edges'] ?? [];

    // Layer order (top → bottom in the TD diagram)
    $lOrder  = ['controller','job','event','listener','service','repository','model','database'];
    $lLabels = [
        'controller' => 'Controllers',
        'job'        => 'Jobs',
        'event'      => 'Events',
        'listener'   => 'Listeners',
        'service'    => 'Services',
        'repository' => 'Repositories',
        'model'      => 'Models',
        'database'   => 'Database',
    ];
    $byLayer = array_fill_keys($lOrder, []);
    foreach ($depNodes as $n) {
        $l = $n['layer'] ?? 'model';
        if (isset($byLayer[$l])) $byLayer[$l][] = $n['name'];
    }

    // Edge label mapping
    $edgeLabel = ['injects' => '', 'uses' => 'uses', 'triggers' => 'triggers', 'persists' => 'persists'];

    // Build flowchart TD
    $fLines = ['flowchart TD'];

    foreach ($lOrder as $l) {
        if (empty($byLayer[$l])) continue;
        if ($l === 'database') {
            // Database uses cylinder shape — defined inline in edges, not in subgraph
            continue;
        }
        $fLines[] = '    subgraph ' . $lLabels[$l];
        foreach ($byLayer[$l] as $nm) { $fLines[] = '        ' . $nm; }
        $fLines[] = '    end';
    }

    // Database node (cylinder shape) — add outside subgraphs
    if (!empty($byLayer['database'])) {
        $fLines[] = '    Database[("Database")]';
    }

    // Edges with optional labels
    foreach ($depEdges as $e) {
        $label = $edgeLabel[$e['type'] ?? ''] ?? '';
        $arrow = $label ? "-->|\"{$label}\"|" : '-->';
        $fLines[] = "    {$e['from']} {$arrow} {$e['to']}";
    }

    // Class styles for each layer
    foreach ($depNodes as $n) {
        $fLines[] = "    class {$n['name']} {$n['layer']}";
    }

    $fLines[] = '    classDef controller fill:#EAF2FF,stroke:#0052CC,color:#172B4D';
    $fLines[] = '    classDef service    fill:#E3FCEF,stroke:#00875A,color:#172B4D';
    $fLines[] = '    classDef repository fill:#FFFAE6,stroke:#FF8B00,color:#172B4D';
    $fLines[] = '    classDef model      fill:#F3F0FF,stroke:#6554C0,color:#172B4D';
    $fLines[] = '    classDef job        fill:#FFF4E5,stroke:#FF8B00,color:#172B4D';
    $fLines[] = '    classDef event      fill:#FFF0FB,stroke:#BF40BF,color:#172B4D';
    $fLines[] = '    classDef listener   fill:#FEE4FA,stroke:#DA62AC,color:#172B4D';
    $fLines[] = '    classDef database   fill:#F4F5F7,stroke:#6B778C,color:#172B4D';

    $depCode = implode("\n", $fLines);

    // Layer counts for legend
    $lCounts = [];
    foreach ($depNodes as $n) { $lCounts[$n['layer']] = ($lCounts[$n['layer']] ?? 0) + 1; }

    $legendItems = [
        'controller' => ['Controllers', '#0052CC', '#EAF2FF'],
        'service'    => ['Services',    '#00875A', '#E3FCEF'],
        'repository' => ['Repositories','#FF8B00', '#FFFAE6'],
        'model'      => ['Models',      '#6554C0', '#F3F0FF'],
        'job'        => ['Jobs',        '#FF8B00', '#FFF4E5'],
        'event'      => ['Events',      '#BF40BF', '#FFF0FB'],
        'listener'   => ['Listeners',   '#DA62AC', '#FEE4FA'],
        'database'   => ['Database',    '#6B778C', '#F4F5F7'],
    ];
    @endphp

    @if(empty($depEdges))
    <div class="atlas-card" style="text-align:center;padding:48px;">
        <p style="color:var(--text-dim);font-weight:500;">No dependency edges found yet.</p>
        <p style="color:var(--text-faint);font-size:13px;margin-top:8px;">Add classes like <code>ProductService</code>, <code>ProductRepository</code> with constructor injection to see the graph.</p>
    </div>
    @else
    <div class="atlas-card" style="padding:0;overflow:hidden;">
        {{-- Legend + controls --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:12px 16px;padding:12px 16px;border-bottom:1px solid var(--border);background:var(--bg-hover);">
            @foreach($legendItems as $layer => [$label, $border, $bg])
            @if(isset($lCounts[$layer]))
            <span style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-dim);font-family:var(--font-mono);">
                <span style="width:10px;height:10px;border-radius:3px;border:1px solid {{ $border }};background:{{ $bg }};display:inline-block;"></span>
                {{ $label }} <span style="font-weight:700;color:var(--text);">{{ $lCounts[$layer] }}</span>
            </span>
            @endif
            @endforeach
            <div style="margin-left:auto;display:flex;align-items:center;gap:4px;">
                <button onclick="depZoom(0.15)" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;font-size:16px;">+</button>
                <button onclick="depZoom(-0.15)" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;font-size:16px;">−</button>
                <button onclick="depFit()" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;" title="Fit all">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
                <button onclick="depClearHighlight()" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;" title="Clear selection">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Hint bar --}}
        <div style="padding:6px 16px;background:var(--bg-hover);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">Scroll to zoom · Drag to pan · Click a node to highlight connections</p>
            <span id="dep-sel-label" style="font-size:11px;color:var(--cyan);font-family:var(--font-mono);font-weight:600;display:none;"></span>
        </div>

        {{-- Custom SVG graph --}}
        <div style="position:relative;height:600px;">
            <svg id="dep-canvas" width="100%" height="100%" style="cursor:grab;background:var(--bg-sunken)">
                <defs>
                    <marker id="dep-arr" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                        <path d="M0,0 L0,6 L7,3 z" fill="rgba(148,178,222,0.5)"/>
                    </marker>
                    <marker id="dep-arr-hi" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                        <path d="M0,0 L0,6 L7,3 z" fill="#0052CC"/>
                    </marker>
                    <filter id="dep-shadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(23,43,77,0.10)" flood-opacity="1"/>
                    </filter>
                </defs>
                <g id="dep-vp">
                    <g id="dep-bands-g"></g>
                    <g id="dep-edges-g"></g>
                    <g id="dep-nodes-g"></g>
                </g>
            </svg>
        </div>
    </div>
    @endif
</section>

{{-- ══ MODULE EXPLORER ══ --}}
<section id="sec-modules" class="p-6" style="display:none">

    @php $modules = $data['modules'] ?? []; @endphp

    <div style="margin-bottom:24px;">
        <h1 class="sec-title">Module Explorer</h1>
        <p class="sec-sub">
            {{ count($modules) }} module{{ count($modules) !== 1 ? 's' : '' }} detected
            @if(count($modules) > 0)
            · {{ array_sum(array_column($modules, 'controllers')) }} controllers
            · {{ array_sum(array_column($modules, 'models')) }} models
            · {{ array_sum(array_column($modules, 'routes')) }} routes
            @endif
        </p>
    </div>

    @if(empty($modules))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No modules detected</p>
        <p style="font-size:13px;color:var(--text-faint);">Create a <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">Modules/</code> directory at your project root with subfolders per module.</p>
        <p style="font-size:12px;color:var(--text-faint);margin-top:8px;">Compatible with <a style="color:var(--cyan);" href="https://nwidart.com/laravel-modules" target="_blank">nwidart/laravel-modules</a> structure.</p>
    </div>
    @else

    {{-- Summary bar --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:24px;">
        @php
        $totalCtrl  = array_sum(array_column($modules, 'controllers'));
        $totalModel = array_sum(array_column($modules, 'models'));
        $totalRoute = array_sum(array_column($modules, 'routes'));
        $totalSvc   = array_sum(array_column($modules, 'services'));
        @endphp
        <div class="kpi-card">
            <span class="kpi-card__label">Controllers</span>
            <span class="kpi-card__num" style="color:var(--violet);">{{ $totalCtrl }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-card__label">Models</span>
            <span class="kpi-card__num" style="color:var(--cyan);">{{ $totalModel }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-card__label">Routes</span>
            <span class="kpi-card__num" style="color:var(--emerald);">{{ $totalRoute }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-card__label">Services</span>
            <span class="kpi-card__num" style="color:var(--sky);">{{ $totalSvc }}</span>
        </div>
    </div>

    {{-- Module cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
        @php
        $modPalette = [
            ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.15)','border'=>'rgba(167,139,250,.3)'],
            ['color'=>'#0052CC','bg'=>'rgba(0,82,204,.15)','border'=>'rgba(0,82,204,.3)'],
            ['color'=>'#34D399','bg'=>'rgba(52,211,153,.15)','border'=>'rgba(52,211,153,.3)'],
            ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.15)','border'=>'rgba(96,165,250,.3)'],
            ['color'=>'#F87171','bg'=>'rgba(248,113,113,.15)','border'=>'rgba(248,113,113,.3)'],
            ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.15)','border'=>'rgba(251,191,36,.3)'],
            ['color'=>'#2DD4BF','bg'=>'rgba(45,212,191,.15)','border'=>'rgba(45,212,191,.3)'],
            ['color'=>'#FB923C','bg'=>'rgba(251,146,60,.15)','border'=>'rgba(251,146,60,.3)'],
            ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.15)','border'=>'rgba(232,121,249,.3)'],
            ['color'=>'#38BDF8','bg'=>'rgba(56,189,248,.15)','border'=>'rgba(56,189,248,.3)'],
        ];
        @endphp
        @foreach($modules as $i => $mod)
        @php
        $mp      = $modPalette[$i % count($modPalette)];
        $initial = strtoupper(substr($mod['name'], 0, 1));
        $hasExtras = $mod['jobs'] > 0 || $mod['events'] > 0 || $mod['services'] > 0;
        @endphp
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s;" onmouseenter="this.style.borderColor='{{ $mp['border'] }}';this.style.transform='translateY(-3px)'" onmouseleave="this.style.borderColor='var(--border)';this.style.transform=''">
            {{-- Top glow bar --}}
            <div style="height:3px;background:linear-gradient(90deg,{{ $mp['color'] }},transparent);"></div>
            {{-- Header --}}
            <div style="display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid var(--border);">
                <div style="width:42px;height:42px;border-radius:12px;background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border:1px solid {{ $mp['border'] }};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;flex:none;">{{ $initial }}</div>
                <div style="min-width:0;flex:1;">
                    <p style="font-weight:700;font-size:14px;color:var(--text);line-height:1.25;">{{ $mod['name'] }}</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px;">{{ $mod['path'] }}</p>
                </div>
            </div>
            {{-- Core stats --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid var(--border);">
                <div style="text-align:center;padding:12px 8px;border-right:1px solid var(--border);">
                    <p style="font-size:20px;font-weight:800;color:var(--violet);font-family:var(--font-sans);">{{ $mod['controllers'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Controllers</p>
                </div>
                <div style="text-align:center;padding:12px 8px;border-right:1px solid var(--border);">
                    <p style="font-size:20px;font-weight:800;color:var(--cyan);font-family:var(--font-sans);">{{ $mod['models'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Models</p>
                </div>
                <div style="text-align:center;padding:12px 8px;">
                    <p style="font-size:20px;font-weight:800;color:var(--emerald);font-family:var(--font-sans);">{{ $mod['routes'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Routes</p>
                </div>
            </div>
            {{-- Extra chips --}}
            <div style="padding:12px 18px;display:flex;flex-wrap:wrap;gap:6px;">
                @if($mod['jobs'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--amber);background:rgba(251,191,36,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(251,191,36,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                    {{ $mod['jobs'] }} Jobs
                </span>
                @endif
                @if($mod['events'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--violet);background:rgba(167,139,250,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(167,139,250,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    {{ $mod['events'] }} Events
                </span>
                @endif
                @if($mod['services'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--sky);background:rgba(96,165,250,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(96,165,250,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    {{ $mod['services'] }} Services
                </span>
                @endif
                @if(!$hasExtras)
                <span style="font-size:11px;color:var(--text-faint);font-style:italic;">No jobs, events, or services</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</section>

{{-- ══ PACKAGE DETECTION ══ --}}
<section id="sec-packages" class="p-6" style="display:none">

    @php
    $packages   = $data['packages'] ?? [];
    $byCategory = [];
    foreach ($packages as $pkg) {
        $byCategory[$pkg['category']][] = $pkg;
    }
    ksort($byCategory);

    $categoryColors = [
        'Admin Panel'       => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.12)','border'=>'rgba(251,191,36,.25)'],
        'API Authentication'=> ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)','border'=>'rgba(96,165,250,.25)'],
        'Architecture'      => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)','border'=>'rgba(167,139,250,.25)'],
        'Audit'             => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.12)','border'=>'rgba(248,113,113,.25)'],
        'Auth Scaffolding'  => ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.12)','border'=>'rgba(232,121,249,.25)'],
        'Authorization'     => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.12)','border'=>'rgba(52,211,153,.25)'],
        'Backup'            => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.12)','border'=>'rgba(52,211,153,.25)'],
        'Debug'             => ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)','border'=>'rgba(142,155,184,.2)'],
        'Import / Export'   => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.12)','border'=>'rgba(52,211,153,.25)'],
        'Media'             => ['color'=>'#0052CC','bg'=>'rgba(0,82,204,.12)','border'=>'rgba(0,82,204,.25)'],
        'Payments'          => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)','border'=>'rgba(167,139,250,.25)'],
        'PDF'               => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.12)','border'=>'rgba(248,113,113,.25)'],
        'Queue Monitoring'  => ['color'=>'#2DD4BF','bg'=>'rgba(45,212,191,.12)','border'=>'rgba(45,212,191,.25)'],
        'Search'            => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)','border'=>'rgba(96,165,250,.25)'],
        'UI Framework'      => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)','border'=>'rgba(167,139,250,.25)'],
    ];
    $defaultCatColor = ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)','border'=>'rgba(142,155,184,.2)'];

    $dotHexColors = [
        'pink'=>'#F472B6','purple'=>'#C084FC','red'=>'#F87171','blue'=>'#60A5FA',
        'orange'=>'#FB923C','violet'=>'#A78BFA','amber'=>'#FBBF24',
        'sky'=>'#38BDF8','emerald'=>'#34D399','green'=>'#4ADE80',
        'teal'=>'#2DD4BF','slate'=>'#6B778C','cyan'=>'#0052CC','indigo'=>'#818CF8',
        'rose'=>'#FB7185',
    ];
    @endphp

    <div style="margin-bottom:24px;">
        <h1 class="sec-title">Packages</h1>
        <p class="sec-sub">
            {{ count($packages) }} known package{{ count($packages) !== 1 ? 's' : '' }} detected
            · {{ count($byCategory) }} {{ count($byCategory) !== 1 ? 'categories' : 'category' }}
        </p>
    </div>

    @if(empty($packages))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No known packages detected</p>
        <p style="font-size:13px;color:var(--text-faint);">None of the tracked packages appear in your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">composer.json</code>.</p>
    </div>
    @else

    @foreach($byCategory as $category => $pkgs)
    @php $catColor = $categoryColors[$category] ?? $defaultCatColor; @endphp
    <div style="margin-bottom:28px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <span style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);">{{ $category }}</span>
            <span style="font-family:var(--font-mono);font-size:10px;padding:2px 8px;border-radius:10px;background:{{ $catColor['bg'] }};color:{{ $catColor['color'] }};border:1px solid {{ $catColor['border'] }};">{{ count($pkgs) }}</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;">
            @foreach($pkgs as $pkg)
            @php $dotHex = $dotHexColors[$pkg['color']] ?? '#6B778C'; @endphp
            <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .18s;" onmouseenter="this.style.borderColor='{{ $dotHex }}55'" onmouseleave="this.style.borderColor='var(--border)'">
                <div style="height:2px;background:{{ $dotHex }};opacity:.6;"></div>
                <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;flex:1;">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $dotHex }};flex:none;margin-top:4px;"></div>
                    <div style="min-width:0;flex:1;">
                        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:4px;">
                            <p style="font-weight:700;font-size:13px;color:var(--text);line-height:1.25;">{{ $pkg['name'] }}</p>
                            @if($pkg['version'])
                            <span style="font-family:var(--font-mono);font-size:10px;background:var(--bg-hover);color:var(--text-faint);padding:1px 6px;border-radius:4px;">v{{ $pkg['version'] }}</span>
                            @endif
                            @if($pkg['dev'])
                            <span style="font-family:var(--font-mono);font-size:10px;color:var(--amber);background:rgba(251,191,36,.1);padding:1px 6px;border-radius:4px;border:1px solid rgba(251,191,36,.2);">dev</span>
                            @endif
                        </div>
                        <p style="font-size:12px;color:var(--text-faint);line-height:1.5;margin-bottom:4px;">{{ $pkg['description'] }}</p>
                        <p style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);opacity:.6;">{{ $pkg['key'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @endif

</section>

{{-- Export --}}
<section id="sec-export" class="p-6" style="display:none">
    <h1 class="sec-title" style="margin-bottom:6px;">Export Architecture</h1>
    <p class="sec-sub" style="margin-bottom:30px;">Download your architecture report in multiple formats for sharing, documentation, or archiving.</p>

    @php
    $exportPath = rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/') . '/' . ltrim(config('architecture-discovery.dashboard.path', 'architecture'), '/');
    @endphp

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;max-width:880px;">

        {{-- JSON --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:14px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.25);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:var(--amber);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">JSON</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture.json</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Full raw report data — all components, routes, dependencies, and scores in machine-readable format. Useful for CI pipelines and tooling integrations.</p>
            <div style="display:flex;gap:8px;margin-top:auto;">
                <button onclick="exportJson()" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);color:var(--amber);font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);transition:background .15s;" onmouseenter="this.style.background='rgba(251,191,36,.25)'" onmouseleave="this.style.background='rgba(251,191,36,.15)'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
                <button onclick="copyJson()" id="copy-json-btn" title="Copy to clipboard" style="padding:8px 12px;background:var(--bg-hover);border:1px solid var(--border);border-radius:8px;color:var(--text-faint);cursor:pointer;transition:border-color .15s;" onmouseenter="this.style.borderColor='var(--amber)'" onmouseleave="this.style.borderColor='var(--border)'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        </div>

        {{-- Markdown --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:14px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(142,155,184,.1);border:1px solid rgba(142,155,184,.2);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:var(--text-dim);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">Markdown</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture.md</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Human-readable report with summary tables, model relationships, and a Mermaid dependency graph. Renders beautifully on GitHub and Notion.</p>
            <button onclick="exportMarkdown()" style="display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(142,155,184,.1);border:1px solid rgba(142,155,184,.2);color:var(--text-dim);font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);margin-top:auto;transition:background .15s;" onmouseenter="this.style.background='rgba(142,155,184,.2)'" onmouseleave="this.style.background='rgba(142,155,184,.1)'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </button>
        </div>

        {{-- HTML --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:14px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(251,146,60,.12);border:1px solid rgba(251,146,60,.25);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:#FB923C;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">HTML</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture.html</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Fully self-contained HTML report. Open in any browser, attach to Jira tickets, or share with stakeholders with no server required.</p>
            <a href="{{ $exportPath }}/export/html" download="architecture.html" style="display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(251,146,60,.15);border:1px solid rgba(251,146,60,.3);color:#FB923C;font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);margin-top:auto;text-decoration:none;transition:background .15s;" onmouseenter="this.style.background='rgba(251,146,60,.25)'" onmouseleave="this.style.background='rgba(251,146,60,.15)'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
        </div>

        {{-- Graphic Report --}}
        <div style="background:var(--bg-elevated);border:2px solid rgba(167,139,250,.35);border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:14px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;right:0;background:var(--violet);color:#fff;font-size:10px;font-weight:800;font-family:var(--font-mono);padding:4px 10px;border-radius:0 0 0 10px;letter-spacing:.08em;">NEW</div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(167,139,250,.12);border:1px solid rgba(167,139,250,.25);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:var(--violet);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">Graphic Report</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture-report.html</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Beautiful standalone HTML report with SVG charts, score gauge, route distribution, dependency graph, and full component tables. No server required.</p>
            <button onclick="exportGraphicHTML()" id="graphic-report-btn" style="display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(167,139,250,.15);border:1px solid rgba(167,139,250,.3);color:var(--violet);font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);margin-top:auto;transition:background .15s;" onmouseenter="this.style.background='rgba(167,139,250,.25)'" onmouseleave="this.style.background='rgba(167,139,250,.15)'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span id="graphic-report-label">Generate &amp; Download</span>
            </button>
        </div>

    </div>

    {{-- CLI hint --}}
    <div style="margin-top:32px;max-width:660px;background:var(--bg-sunken);border:1px solid var(--border);border-radius:14px;padding:20px;box-shadow:var(--shadow);">
        <p style="font-size:12px;color:var(--text-faint);margin-bottom:12px;font-weight:600;font-family:var(--font-mono);">Export from the command line</p>
        <div style="display:flex;flex-direction:column;gap:8px;font-family:var(--font-mono);font-size:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--text-faint);">$</span>
                <span style="color:var(--emerald);">php artisan architecture:discover</span>
                <span style="color:var(--text-faint);font-size:11px;margin-left:4px;">— exports json + html (configured formats)</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--text-faint);">$</span>
                <span style="color:var(--emerald);">php artisan architecture:discover <span style="color:var(--amber);">--format=svg</span></span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--text-faint);">$</span>
                <span style="color:var(--emerald);">php artisan architecture:discover <span style="color:var(--amber);">--format=markdown --output=docs/architecture.md</span></span>
            </div>
        </div>
    </div>


</section>

{{-- AI Insights --}}
<section id="sec-ai" class="p-6" style="display:none">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
        <h1 class="sec-title">AI Insights</h1>
        @if(config('architecture-discovery.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            AI Ready · {{ config('architecture-discovery.ai.model', 'gemini-2.5-flash') }}
        </span>
        @else
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--text-faint);background:var(--bg-hover);border:1px solid var(--border);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--text-faint);"></span>
            AI Disabled
        </span>
        @endif
    </div>
    <p class="sec-sub" style="margin-bottom:24px;">AI-powered architecture review — score, SOLID analysis, code smells, and actionable suggestions.</p>

    @if(!config('architecture-discovery.ai.enabled', false))
    {{-- Setup card --}}
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:12px;padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:flex-start;gap:12px;">
            <svg style="width:20px;height:20px;color:var(--amber);margin-top:2px;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p style="font-weight:700;color:var(--amber);margin-bottom:8px;">AI is not enabled</p>
                <p style="font-size:13px;color:var(--text-dim);margin-bottom:12px;">To enable AI insights, add the following to your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">.env</code> file and publish the config:</p>
                <div style="background:var(--bg-sunken);border-radius:8px;padding:12px;font-family:var(--font-mono);font-size:12px;color:var(--emerald);margin-bottom:10px;">
                    GEMINI_API_KEY=your_api_key_here
                </div>
                <p style="font-size:13px;color:var(--text-dim);margin-bottom:8px;">Then in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">config/architecture-discovery.php</code>:</p>
                <div style="background:var(--bg-sunken);border-radius:8px;padding:12px;font-family:var(--font-mono);font-size:12px;color:var(--text);">
                    'ai' => [<br>
                    &nbsp;&nbsp;'enabled' => <span style="color:var(--emerald);">true</span>,<br>
                    &nbsp;&nbsp;'provider' => 'gemini',<br>
                    &nbsp;&nbsp;'model' => 'gemini-2.5-flash',<br>
                    ]
                </div>
                <p style="font-size:11px;color:var(--text-faint);margin-top:10px;font-family:var(--font-mono);">Get a free API key at aistudio.google.com</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Analyze button --}}
    <div id="ai-trigger" style="margin-bottom:24px;">
        <button onclick="aiAnalyze()"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:rgba(0,82,204,0.15);border:1px solid rgba(0,82,204,0.4);border-radius:10px;color:var(--cyan);font-weight:600;font-size:13px;cursor:pointer;transition:background .2s;font-family:var(--font-mono);">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            Analyze with AI
        </button>
        <p style="font-size:12px;color:var(--text-faint);margin-top:8px;font-family:var(--font-mono);">Sends your architecture data to {{ config('architecture-discovery.ai.model', 'gemini-2.5-flash') }} for analysis. Takes 10–30 seconds.</p>
    </div>

    {{-- Loading state --}}
    <div id="ai-loading" style="display:none;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:12px;color:var(--cyan);">
            <svg style="width:20px;height:20px;animation:radarSpin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span style="font-size:13px;font-weight:600;font-family:var(--font-mono);">Analyzing architecture with AI…</span>
        </div>
        <p style="font-size:12px;color:var(--text-faint);margin-top:6px;margin-left:32px;font-family:var(--font-mono);">This usually takes 10–30 seconds.</p>
    </div>

    {{-- Error state --}}
    <div id="ai-error" style="display:none;margin-bottom:24px;max-width:560px;background:rgba(248,113,113,0.08);border:1px solid rgba(248,113,113,0.3);border-radius:12px;padding:16px;">
        <p style="font-size:13px;font-weight:600;color:var(--rose);margin-bottom:4px;">Analysis failed</p>
        <p id="ai-error-msg" style="font-size:12px;color:var(--rose);font-family:var(--font-mono);"></p>
    </div>

    {{-- Results --}}
    <div id="ai-results" style="display:none;max-width:900px;display:flex;flex-direction:column;gap:16px;">

        {{-- Summary + AI Score --}}
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:240px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:10px;">AI Summary</p>
                <p id="ai-summary" style="font-size:13px;color:var(--text-dim);line-height:1.65;"></p>
            </div>
            <div style="width:140px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;flex:none;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);">AI Score</p>
                <p id="ai-score-num" style="font-size:40px;font-weight:800;color:var(--cyan);font-family:var(--font-sans);line-height:1;"></p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">/ 100</p>
                <div style="width:100%;height:4px;background:var(--bg-hover);border-radius:4px;margin-top:4px;overflow:hidden;">
                    <div id="ai-score-bar" style="height:4px;border-radius:4px;background:linear-gradient(90deg,var(--cyan),var(--violet));transition:width .7s ease;width:0%;"></div>
                </div>
            </div>
        </div>

        {{-- SOLID Review --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">SOLID Principles</p>
            <div id="ai-solid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;"></div>
        </div>

        {{-- Problems --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Problems Detected</p>
            <div id="ai-problems" style="display:flex;flex-direction:column;gap:10px;"></div>
        </div>

        {{-- Suggestions --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Suggestions</p>
            <div id="ai-suggestions" style="display:flex;flex-direction:column;gap:10px;"></div>
        </div>

        {{-- Laravel Best Practices --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Laravel Best Practices</p>
            <div id="ai-laravel-practices" style="display:flex;flex-direction:column;gap:8px;"></div>
        </div>

        {{-- Best Practices (followed) --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:12px;">Practices Already Followed</p>
            <ul id="ai-best-practices" style="display:flex;flex-direction:column;gap:6px;"></ul>
        </div>

        {{-- Re-analyze --}}
        <div style="display:flex;align-items:center;gap:12px;padding-top:4px;">
            <button onclick="aiAnalyze()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--bg-elevated);border:1px solid var(--border);color:var(--text-dim);font-size:12px;border-radius:9px;cursor:pointer;transition:border-color .15s;" onmouseenter="this.style.borderColor='var(--cyan)';this.style.color='var(--cyan)'" onmouseleave="this.style.borderColor='var(--border)';this.style.color='var(--text-dim)'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Re-analyze
            </button>
            <span id="ai-provider-badge" style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);"></span>
        </div>

    </div>

</section>

{{-- ══ AI CHAT ══ --}}
<section id="sec-chat" style="display:none;flex-direction:column;height:100%;padding:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
        <h1 class="sec-title">AI Chat</h1>
        @if(config('architecture-discovery.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            {{ config('architecture-discovery.ai.model') }}
        </span>
        @endif
    </div>
    <p class="sec-sub" style="margin-bottom:20px;">Ask anything. The package finds the relevant controllers, models, and routes in your architecture — then sends only that to AI.</p>

    @if(!config('architecture-discovery.ai.enabled', false))
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:10px;padding:16px;margin-bottom:20px;">
        <p style="font-size:13px;color:var(--amber);">AI is not enabled. Set <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">ai.enabled = true</code> and <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    {{-- Suggestion pills --}}
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;" id="chat-suggestions">
        <button onclick="chatSuggest('Which controller has the most methods?')" class="ctrl-chip" style="cursor:pointer;border-radius:20px;color:var(--cyan);border-color:rgba(0,82,204,0.3);padding:5px 12px;">Which controller is largest?</button>
        <button onclick="chatSuggest('Trace the main request flow from route through controller to model.')" class="ctrl-chip" style="cursor:pointer;border-radius:20px;color:var(--cyan);border-color:rgba(0,82,204,0.3);padding:5px 12px;">Trace request flow</button>
        <button onclick="chatSuggest('Are there any SOLID principle violations?')" class="ctrl-chip" style="cursor:pointer;border-radius:20px;color:var(--cyan);border-color:rgba(0,82,204,0.3);padding:5px 12px;">SOLID violations?</button>
        <button onclick="chatSuggest('Which models have the most relationships?')" class="ctrl-chip" style="cursor:pointer;border-radius:20px;color:var(--cyan);border-color:rgba(0,82,204,0.3);padding:5px 12px;">Models with most relationships</button>
        <button onclick="chatSuggest('What services should I extract from my controllers?')" class="ctrl-chip" style="cursor:pointer;border-radius:20px;color:var(--cyan);border-color:rgba(0,82,204,0.3);padding:5px 12px;">Suggest service extractions</button>
        <button onclick="chatSuggest('Explain the overall architecture and data flow.')" class="ctrl-chip" style="cursor:pointer;border-radius:20px;color:var(--cyan);border-color:rgba(0,82,204,0.3);padding:5px 12px;">Overall architecture</button>
    </div>

    {{-- Messages --}}
    <div id="chat-messages" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;max-height:calc(100vh - 420px);min-height:200px;padding-right:4px;">
        <div id="chat-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:200px;color:var(--text-faint);">
            <svg style="width:40px;height:40px;margin-bottom:12px;color:var(--border-strong);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p style="font-size:13px;">Ask a question about your architecture</p>
        </div>
    </div>

    {{-- Input --}}
    <div style="border:1px solid var(--border);border-radius:10px;background:var(--bg-elevated);overflow:hidden;">
        <textarea id="chat-input" rows="2"
            placeholder="e.g. Trace the main request flow  •  Which controller is too large?  •  Where should I add a service?"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            oninput="chatPreviewContext(this.value)"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();chatSend();}"
            style="width:100%;padding:12px 16px 4px;font-size:13px;color:var(--text);background:transparent;resize:none;outline:none;border:none;font-family:var(--font-sans);box-sizing:border-box;"></textarea>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 16px 12px;">
            <span id="chat-context-hint" style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;"></span>
            <button onclick="chatSend()" id="chat-send-btn"
                {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
                style="display:flex;align-items:center;gap:6px;padding:6px 16px;background:rgba(0,82,204,0.15);border:1px solid rgba(0,82,204,0.4);border-radius:8px;color:var(--cyan);font-size:12px;font-weight:600;cursor:pointer;font-family:var(--font-mono);flex:none;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send
            </button>
        </div>
    </div>
</section>

{{-- ══ AI DOCS ══ --}}
<section id="sec-aidocs" class="p-6" style="display:none">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
        <h1 class="sec-title">AI Documentation</h1>
        @if(config('architecture-discovery.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            {{ config('architecture-discovery.ai.model') }}
        </span>
        @endif
    </div>
    <p class="sec-sub" style="margin-bottom:24px;">AI writes full markdown docs for each layer of your architecture. One click per file — or generate all at once.</p>

    @if(!config('architecture-discovery.ai.enabled', false))
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:10px;padding:16px;margin-bottom:24px;">
        <p style="font-size:13px;color:var(--amber);">AI is not enabled. Set <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">ai.enabled = true</code> and <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <button onclick="docsGenerateAll()"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            class="atlas-btn atlas-btn--cyan"
            style="padding:9px 18px;font-size:13px;border-radius:10px;opacity:{{ config('architecture-discovery.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('architecture-discovery.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Generate All Docs
        </button>
        <button onclick="docsDownloadAll()" id="docs-download-all-btn" class="atlas-btn" style="display:none;padding:9px 18px;font-size:13px;border-radius:10px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download All
        </button>

        {{-- AI Graphic Report button --}}
        <button onclick="generateAIGraphicReport()" id="ai-report-btn"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            class="atlas-btn"
            style="padding:9px 18px;font-size:13px;border-radius:10px;border-color:rgba(167,139,250,0.4);color:var(--violet);background:rgba(167,139,250,0.08);opacity:{{ config('architecture-discovery.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('architecture-discovery.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span id="ai-report-btn-label">Generate AI Graphic Report</span>
        </button>
    </div>

    {{-- AI Report progress panel --}}
    <div id="ai-report-progress" style="display:none;max-width:480px;margin-bottom:32px;border-radius:16px;overflow:hidden;border:1px solid rgba(167,139,250,0.3);background:var(--bg-elevated);">
        <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:rgba(167,139,250,0.15);border-bottom:1px solid rgba(167,139,250,0.2);">
            <svg style="width:16px;height:16px;animation:spin 1s linear infinite;flex-shrink:0;color:var(--violet);" fill="none" viewBox="0 0 24 24" id="ai-report-spinner">
                <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p style="font-size:13px;font-weight:600;color:var(--violet);font-family:var(--font-mono);" id="ai-report-progress-title">Generating AI Report…</p>
        </div>
        <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;" id="ai-report-steps">
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="analyze">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Analyzing architecture with AI</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="architecture">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Architecture documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="models">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Models documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="controllers">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Controllers documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="routes">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Routes documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="services">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Services documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="modules">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Modules documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="build">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Building graphic report</span>
            </div>
        </div>
        <div id="ai-report-error" style="display:none;padding:0 20px 16px;font-size:13px;color:var(--rose);font-family:var(--font-mono);"></div>
    </div>

    {{-- Doc cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;max-width:880px;" id="docs-grid">

        @php
        $docTypes = [
            ['architecture', 'Architecture.md', 'Overall design, patterns, score, strengths & improvements.', '#4f46e5', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['models',       'Models.md',       'Each model — purpose, fields, relationships, business role.', '#8b5cf6', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
            ['controllers',  'Controllers.md',  'Each controller — responsibilities, methods, observations.',  '#3b82f6', 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
            ['routes',       'Routes.md',       'All routes grouped by resource — purpose, auth, middleware.',  '#10b981', 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['services',     'Services.md',     'Service layer, repositories, jobs, and events explained.',    '#f59e0b', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['modules',      'Modules.md',      'Module breakdown — domain responsibilities and coupling.',    '#06b6d4', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ];
        @endphp

        @foreach($docTypes as [$type, $filename, $desc, $color, $icon])
        <div class="card" id="doc-card-{{ $type }}" style="padding:20px;display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $color }}18;border:1px solid {{ $color }}40">
                        <svg style="width:16px;height:16px;color:{{ $color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <p style="font-weight:600;color:var(--text);font-size:13px;font-family:var(--font-mono);">{{ $filename }}</p>
                    </div>
                </div>
                <span id="doc-status-{{ $type }}" style="font-size:11px;color:var(--text-faint);flex-shrink:0;font-family:var(--font-mono);">Pending</span>
            </div>
            <p style="font-size:12px;color:var(--text-dim);line-height:1.6;">{{ $desc }}</p>
            <div style="display:flex;gap:8px;margin-top:auto;padding-top:4px;">
                <button onclick="docsGenerate('{{ $type }}')"
                    {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
                    id="doc-gen-btn-{{ $type }}"
                    class="atlas-btn atlas-btn--cyan"
                    style="flex:1;justify-content:center;font-size:11px;padding:7px 10px;border-radius:8px;opacity:{{ config('architecture-discovery.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('architecture-discovery.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Generate
                </button>
                <button onclick="docsDownload('{{ $type }}')"
                    id="doc-dl-btn-{{ $type }}"
                    class="atlas-btn"
                    style="display:none;align-items:center;justify-content:center;gap:6px;font-size:11px;padding:7px 10px;border-radius:8px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
            </div>
        </div>
        @endforeach

    </div>

</section>

</main>

<script>
const APP = @json($data);
const SECTIONS = ['overview','modules','packages','models','modelmap','controllers','routes','apidocs','jobs','events','services','repositories','observers','policies','dependencies','export','ai','chat','aidocs'];
let depRendered     = false;
let mapTreeRendered = false;
let erRendered      = false;
let graphRendered   = false;

const erByModel  = @json($mmFocused ?? []);
const erAllCode  = @json($mmErCode ?? '');
const erLargeThreshold = 20;

function erFocus(modelName) {
    const el = document.getElementById('er-diagram');
    if (!el) return;
    const code = (modelName === '__all__') ? erAllCode : (erByModel[modelName] || erAllCode);
    if (!code) {
        el.innerHTML = '<p style="color:var(--text-faint);font-size:12px;font-style:italic;">No relationships defined for this model.</p>';
        return;
    }
    el.removeAttribute('data-processed');
    el.innerHTML = '';
    el.textContent = code;
    if (window.mermaid) {
        try { mermaid.run({ nodes: [el] }); } catch(e) {}
    }
}

function navigate(s) {
    SECTIONS.forEach(id => {
        const sec = document.getElementById('sec-' + id);
        if (sec) sec.style.display = id === s ? (id === 'chat' ? 'flex' : 'block') : 'none';
        const nav = document.getElementById('nav-' + id);
        if (nav) {
            nav.classList.toggle('nav-active', id === s);
        }
    });
    // Update topbar breadcrumb
    const sectionNames = {
        overview:'Overview', models:'Models', modelmap:'Relation Graph', controllers:'Controllers',
        routes:'Routes', apidocs:'API Docs', jobs:'Jobs', events:'Events', services:'Services',
        repositories:'Repositories', observers:'Observers', policies:'Policies',
        dependencies:'Dependencies', export:'Export', ai:'AI Insights', chat:'AI Chat',
        aidocs:'AI Docs', modules:'Modules', packages:'Packages'
    };
    const breadcrumb = document.getElementById('topbar-section');
    if (breadcrumb) breadcrumb.textContent = sectionNames[s] || s;
    if (s === 'dependencies' && !depRendered) {
        depRendered = true;
        setTimeout(initDepGraph, 60);
    }
    if (s === 'modelmap' && !graphRendered) {
        setTimeout(initRelGraph, 50);
    }
}

function _atlasTheme(el) {
    // Light theme: Tailwind's native colours are correct — no post-processing needed.
}

function showDetail(type, idx) {
    document.getElementById(type + '-list').style.display = 'none';
    document.getElementById(type + '-detail').style.display = 'block';
    const contentEl = document.getElementById(type + '-detail-content');
    contentEl.innerHTML = renderDetail(type, APP[type][idx]);
    _atlasTheme(contentEl);
}

function showList(type) {
    document.getElementById(type + '-list').style.display = 'block';
    document.getElementById(type + '-detail').style.display = 'none';
}

function filterGrid(type) {
    const q = document.getElementById(type + '-search').value.toLowerCase();
    document.querySelectorAll('#' + type + '-grid [data-name]').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
    const lv = document.getElementById('mds-list-view');
    if (lv && type === 'models') lv.querySelectorAll('[data-name]').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

function filterRoutes() {
    const q   = document.getElementById('routes-search').value.toLowerCase();
    const mf  = document.getElementById('routes-method-filter').value;
    const mwf = document.getElementById('routes-mw-filter').value.toLowerCase();
    document.querySelectorAll('.route-row').forEach(row => {
        const handler  = (row.querySelector('td:nth-child(3)')?.textContent || '').toLowerCase();
        const textOk   = !q || row.dataset.uri.includes(q) || handler.includes(q);
        const methodOk = !mf  || row.dataset.methods.includes(mf);
        const mwOk     = !mwf || row.dataset.mw.includes(mwf);
        row.style.display = textOk && methodOk && mwOk ? '' : 'none';
    });
}

// ── Route Explorer ────────────────────────────────────────────────────────────

const MW_DESC = {
    web: 'Web Session / CSRF', api: 'Stateless API', auth: 'Requires Authentication',
    'auth:sanctum': 'Sanctum Token Auth', 'auth:api': 'API Token Auth',
    guest: 'Redirect if Logged In', verified: 'Email Verified Required',
    signed: 'Requires Signed URL', throttle: 'Rate Limiting',
    can: 'Authorization Gate', bindings: 'Route Model Binding',
    cache: 'Response Caching', cors: 'CORS Headers',
};

function mwDesc(mw) {
    return MW_DESC[mw] || MW_DESC[mw.split(':')[0]] || null;
}

function buildDepGraph() {
    const nodeMap = {}, edgeMap = {};
    for (const n of (APP.dependencies?.nodes || [])) nodeMap[n.name] = n;
    for (const e of (APP.dependencies?.edges || [])) {
        if (!edgeMap[e.from]) edgeMap[e.from] = [];
        edgeMap[e.from].push(e);
    }
    return { nodeMap, edgeMap };
}

function traceChain(startName) {
    const { nodeMap, edgeMap } = buildDepGraph();
    const chain = [], visited = new Set();

    function dfs(name, edgeType) {
        if (visited.has(name)) return;
        visited.add(name);
        chain.push({ name, layer: nodeMap[name]?.layer || 'unknown', edgeType });
        for (const e of (edgeMap[name] || [])) dfs(e.to, e.type);
    }
    dfs(startName, null);

    // ── Smart inference fallback ──────────────────────────────────────────────
    // Runs whenever the chain has no service/repo/model.
    // Works for any naming pattern: UserController, PaymentAnalytics, OrderPage, etc.
    if (!chain.some(n => ['service','repository','model'].includes(n.layer))) {
        // Strip common non-domain suffixes to get the entity base words
        const stripped = startName
            .replace(/Controller|Page|Resource|Component|Widget|Action|Form|Table|Livewire|Panel$/g, '');

        // Split PascalCase → individual words: "PaymentAnalytics" → ["Payment","Analytics"]
        const words = stripped.match(/[A-Z][a-z0-9]*/g) || [stripped];
        // Build a ranked candidate list: [full, longest-word, each-word]
        const candidates = [stripped, ...words.sort((a,b) => b.length - a.length)];

        const depNodes = APP.dependencies?.nodes || [];
        const models   = APP.models || [];

        // Score helper: how well does a node name match the candidate keywords?
        const score = (nodeName, layer) => {
            const n = nodeName.toLowerCase().replace(/(service|repository|repo)$/i,'');
            for (let i = 0; i < candidates.length; i++) {
                const c = candidates[i].toLowerCase();
                if (n === c)           return 100 - i;   // exact match
                if (n.startsWith(c))   return  80 - i;   // prefix match
                if (n.includes(c))     return  60 - i;   // contains
                if (c.includes(n) && n.length >= 3) return 40 - i; // reverse contains
            }
            return 0;
        };

        // Find best-scoring service
        if (!chain.some(n => n.layer === 'service')) {
            const best = depNodes
                .filter(n => n.layer === 'service')
                .map(n => ({ n, s: score(n.name, 'service') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.n.name)) {
                chain.push({ name: best.n.name, layer: 'service', edgeType: 'injects', _inferred: true });
                visited.add(best.n.name);
                for (const e of (edgeMap[best.n.name] || [])) dfs(e.to, e.type);
            }
        }

        // Find best-scoring repository
        if (!chain.some(n => n.layer === 'repository')) {
            const best = depNodes
                .filter(n => n.layer === 'repository')
                .map(n => ({ n, s: score(n.name, 'repository') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.n.name)) {
                chain.push({ name: best.n.name, layer: 'repository', edgeType: 'uses', _inferred: true });
                visited.add(best.n.name);
                for (const e of (edgeMap[best.n.name] || [])) dfs(e.to, e.type);
            }
        }

        // Find best-scoring model
        if (!chain.some(n => n.layer === 'model')) {
            const best = models
                .map(m => ({ m, s: score(m.name, 'model') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.m.name)) {
                chain.push({ name: best.m.name, layer: 'model', edgeType: 'uses', _inferred: true });
                visited.add(best.m.name);
            }
        }
    }

    return chain;
}

const LAYER_STYLE = {
    controller: { color:'#60A5FA', bg:'rgba(96,165,250,.12)',  border:'rgba(96,165,250,.3)',  dot:'#60A5FA' },
    service:    { color:'#34D399', bg:'rgba(52,211,153,.12)',  border:'rgba(52,211,153,.3)',  dot:'#34D399' },
    repository: { color:'#FBBF24', bg:'rgba(251,191,36,.12)',  border:'rgba(251,191,36,.3)',  dot:'#FBBF24' },
    model:      { color:'#A78BFA', bg:'rgba(167,139,250,.12)', border:'rgba(167,139,250,.3)', dot:'#A78BFA' },
    job:        { color:'#FB923C', bg:'rgba(251,146,60,.12)',  border:'rgba(251,146,60,.3)',  dot:'#FB923C' },
    event:      { color:'#E879F9', bg:'rgba(232,121,249,.12)', border:'rgba(232,121,249,.3)', dot:'#E879F9' },
    listener:   { color:'#F472B6', bg:'rgba(244,114,182,.12)', border:'rgba(244,114,182,.3)', dot:'#F472B6' },
    database:   { color:'#6B778C', bg:'rgba(142,155,184,.12)', border:'rgba(142,155,184,.3)', dot:'#6B778C' },
    unknown:    { color:'#6B778C', bg:'rgba(91,103,133,.12)',  border:'rgba(91,103,133,.3)',  dot:'#6B778C' },
};

const EDGE_LABEL = { injects: 'injects', uses: 'uses', triggers: 'triggers', persists: 'persists' };

// ── Route Graph Explorer ───────────────────────────────────────────────────────

const RF_COLOR = {
    // Atlassian light theme: white bg, coloured border/stroke, navy text
    request:    { bg:'#EAF2FF', border:'#0052CC', type:'#0052CC', name:'#172B4D', sub:'#0065FF',  dot:'#0052CC' },
    middleware: { bg:'#FFFAE6', border:'#FF8B00', type:'#FF8B00', name:'#172B4D', sub:'#FF8B00',  dot:'#FF8B00' },
    controller: { bg:'#EAF2FF', border:'#0052CC', type:'#0052CC', name:'#172B4D', sub:'#0052CC',  dot:'#0052CC' },
    service:    { bg:'#E3FCEF', border:'#00875A', type:'#00875A', name:'#172B4D', sub:'#00875A',  dot:'#00875A' },
    repository: { bg:'#FFFAE6', border:'#FF8B00', type:'#FF8B00', name:'#172B4D', sub:'#FF8B00',  dot:'#FF8B00' },
    model:      { bg:'#F3F0FF', border:'#6554C0', type:'#6554C0', name:'#172B4D', sub:'#6554C0',  dot:'#6554C0' },
    database:   { bg:'#F4F5F7', border:'#6B778C', type:'#6B778C', name:'#172B4D', sub:'#6B778C',  dot:'#6B778C' },
    job:        { bg:'#FFF4E5', border:'#FF5630', type:'#FF5630', name:'#172B4D', sub:'#FF5630',  dot:'#FF5630' },
    event:      { bg:'#FFF0FB', border:'#BF40BF', type:'#BF40BF', name:'#172B4D', sub:'#BF40BF',  dot:'#BF40BF' },
    listener:   { bg:'#FEE4FA', border:'#DA62AC', type:'#DA62AC', name:'#172B4D', sub:'#DA62AC',  dot:'#DA62AC' },
    unknown:    { bg:'#F4F5F7', border:'#6B778C', type:'#6B778C', name:'#42526E', sub:'#6B778C',  dot:'#6B778C' },
};
const RF_LAYER_ORDER = ['request','middleware','controller','service','repository','model','database','job','event','listener','unknown'];
const RF_TYPE_LABEL  = { request:'HTTP Request', middleware:'Middleware', controller:'Controller', service:'Service', repository:'Repository', model:'Model', database:'Database', job:'Job', event:'Event', listener:'Listener', unknown:'Component' };

let _rfNodes = {}, _rfEdges = [], _rfSelected = null, _rfTab = 'info', _rfRoute = null, _rfMws = [];

function showRouteDetail(idx) {
    const route   = APP.routes[idx];
    const methods = (route.methods || []).filter(m => m !== 'HEAD');
    const ctrl    = route.controller?.class ? route.controller.class.split('\\').pop() : null;
    const action  = route.controller?.method || null;
    const mws     = route.middleware || [];
    const chain   = ctrl ? traceChain(ctrl) : [];

    _rfRoute = route; _rfMws = mws; _rfNodes = {}; _rfEdges = []; _rfSelected = null; _rfTab = 'info';

    document.getElementById('routes-list').style.display   = 'none';
    document.getElementById('routes-detail').style.display = 'block';

    // ── Build graph nodes + edges ──────────────────────────────────────────────
    let uid = 0;
    const mkNode = (type, name, sub, meta) => {
        const id = 'rg' + (uid++);
        _rfNodes[id] = { id, type, name, sub: sub || '', meta: meta || {} };
        return id;
    };
    const mkEdge = (from, to, label) => _rfEdges.push({ from, to, label: label || '' });

    const routeId = mkNode('request', methods.join('|') + ' /' + (route.uri || ''), route.name || '', { route });
    let prevId = routeId;

    if (mws.length) {
        const mwId = mkNode('middleware', 'Middleware Stack', mws.length + ' layers', { mws });
        mkEdge(prevId, mwId, 'enters');
        prevId = mwId;
    }

    if (chain.length > 0) {
        let lastId = prevId, firstEdge = ctrl ? 'dispatches' : null;
        chain.forEach((node, i) => {
            const isCtrl  = i === 0;
            const sub     = isCtrl ? (action && action !== '__invoke' ? '@' + action : 'Invokable') : (RF_TYPE_LABEL[node.layer] || node.layer);
            const nid     = mkNode(node.layer, node.name, sub, { node, action: isCtrl ? action : null, inferred: !!node._inferred });
            const label   = i === 0 ? firstEdge : (EDGE_LABEL[node.edgeType] || node.edgeType || 'calls');
            mkEdge(lastId, nid, label);
            lastId = nid;
        });
        // DB Table node — find the model node and look up its table
        const modelNode = chain.find(n => n.layer === 'model');
        if (modelNode) {
            const md = (APP.models || []).find(m => m.name === modelNode.name);
            if (md?.table) {
                const dbId     = mkNode('database', md.table, 'DB Table', { table: md.table, model: md });
                const modelNid = Object.values(_rfNodes).find(n => n.name === modelNode.name && n.type === 'model')?.id;
                if (modelNid) mkEdge(modelNid, dbId, 'queries');
            }
        }
    } else if (ctrl) {
        // Controller exists but has no dep-graph edges and inference found nothing
        const cid = mkNode('controller', ctrl, action && action !== '__invoke' ? '@' + action : 'Invokable', {});
        mkEdge(prevId, cid, 'dispatches');
    }

    // ── Layout: group nodes into layers, position each ─────────────────────────
    const NW = 224, NH = 74, GAP_Y = 100, GAP_X = 28, PAD = 40;
    const layers = {};
    Object.values(_rfNodes).forEach(n => {
        const li = RF_LAYER_ORDER.indexOf(n.type);
        const k  = li >= 0 ? li : 99;
        (layers[k] = layers[k] || []).push(n);
    });
    const layerKeys   = Object.keys(layers).map(Number).sort((a, b) => a - b);
    const maxRowW     = Math.max(...layerKeys.map(k => layers[k].length * NW + (layers[k].length - 1) * GAP_X));
    const CANVAS_W    = Math.max(maxRowW + PAD * 2, 480);
    const CANVAS_H    = layerKeys.length * (NH + GAP_Y) + PAD * 2 - GAP_Y + PAD;
    const pos = {};
    layerKeys.forEach((lk, li) => {
        const row = layers[lk];
        const totalW = row.length * NW + (row.length - 1) * GAP_X;
        let x = (CANVAS_W - totalW) / 2;
        const y = PAD + li * (NH + GAP_Y);
        row.forEach(n => {
            pos[n.id] = { x, y, cx: x + NW / 2, cy: y + NH / 2 };
            x += NW + GAP_X;
        });
    });

    // ── SVG defs ───────────────────────────────────────────────────────────────
    const defs = `<defs>
        <pattern id="rf-dot" x="0" y="0" width="22" height="22" patternUnits="userSpaceOnUse">
            <circle cx="1" cy="1" r="0.8" fill="rgba(148,178,222,0.15)" opacity="1"/>
        </pattern>
        <marker id="rf-arr" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="rgba(148,178,222,0.5)"/>
        </marker>
        <marker id="rf-arr-hi" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="#0052CC"/>
        </marker>
        <filter id="rf-glow" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="3" result="blur"/>
            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>`;

    // ── Edges ──────────────────────────────────────────────────────────────────
    const edgesSvg = _rfEdges.map(e => {
        const f = pos[e.from], t = pos[e.to];
        if (!f || !t) return '';
        const x1 = f.cx, y1 = f.y + NH, x2 = t.cx, y2 = t.y;
        const cp = Math.abs(y2 - y1) * 0.45;
        const d  = `M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}`;
        const mx = (x1+x2)/2, my = (y1+y2)/2;
        const lw = (e.label.length * 6) + 14;
        return `<g>
            <path d="${d}" fill="none" stroke="rgba(148,178,222,0.35)" stroke-width="1.5" marker-end="url(#rf-arr)"/>
            ${e.label ? `<rect x="${mx-lw/2}" y="${my-8}" width="${lw}" height="16" rx="8" fill="#FFFFFF" stroke="#DFE1E6" stroke-width="1"/>
            <text x="${mx}" y="${my+4}" fill="#6B778C" font-size="9" font-family="ui-monospace,monospace" text-anchor="middle" font-weight="600" letter-spacing="0.04em">${_esc(e.label)}</text>` : ''}
        </g>`;
    }).join('');

    // ── Nodes ──────────────────────────────────────────────────────────────────
    const nodesSvg = Object.values(_rfNodes).map(n => {
        const p = pos[n.id]; if (!p) return '';
        const c          = RF_COLOR[n.type] || RF_COLOR.unknown;
        const typeLabel  = RF_TYPE_LABEL[n.type] || n.type;
        const shortName  = n.name.length > 24 ? n.name.slice(0, 23) + '…' : n.name;
        const shortSub   = n.sub.length  > 28 ? n.sub.slice(0, 27)  + '…' : n.sub;
        const isInferred = n.meta?.inferred;
        // Inferred nodes get a dashed border + slightly dimmer opacity to show they are convention-based
        const strokeDash = isInferred ? 'stroke-dasharray="5,3"' : '';
        const opacity    = isInferred ? 'opacity="0.88"' : '';
        return `<g class="rf-node" data-id="${n.id}" onclick="rfClick('${n.id}')" style="cursor:pointer" ${opacity}>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="12" fill="${c.bg}" stroke="${c.border}" stroke-width="${isInferred ? 1.5 : 1.5}" ${strokeDash} id="rf-rect-${n.id}"/>
            <rect x="${p.x+1.5}" y="${p.y+1.5}" width="${NW-3}" height="20" rx="10" fill="${c.border}" fill-opacity="${isInferred ? 0.08 : 0.15}"/>
            <circle cx="${p.x+14}" cy="${p.y+11}" r="3.5" fill="${c.dot}"/>
            <text x="${p.x+23}" y="${p.y+15}" fill="${c.type}" font-size="8.5" font-family="ui-monospace,monospace" font-weight="700" letter-spacing="0.1em">${typeLabel.toUpperCase()}${isInferred ? ' ~' : ''}</text>
            <text x="${p.x+12}" y="${p.y+43}" fill="${c.name}" font-size="12.5" font-family="ui-monospace,monospace" font-weight="700">${_esc(shortName)}</text>
            ${shortSub ? `<text x="${p.x+12}" y="${p.y+60}" fill="${c.sub}" font-size="10" font-family="ui-monospace,monospace">${_esc(shortSub)}</text>` : ''}
        </g>`;
    }).join('');

    // ── Method badges (HTML) ───────────────────────────────────────────────────
    const methodBadges = methods.map(m =>
        `<span class="text-xs font-bold px-2.5 py-1 rounded-lg method-${m.toLowerCase()}">${m}</span>`
    ).join('');

    // ── Right panel: default shows route props ─────────────────────────────────
    const firstNode = Object.values(_rfNodes)[0];
    _rfSelected = firstNode;

    document.getElementById('routes-detail-content').innerHTML = `
    <div style="display:flex;height:580px;border-radius:16px;overflow:hidden;border:1px solid var(--border);background:var(--bg-sunken);box-shadow:var(--shadow)">

        <!-- ── Graph canvas ── -->
        <div style="flex:1;display:flex;flex-direction:column;min-width:0;overflow:hidden">

            <!-- Toolbar -->
            <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#F4F5F7;border-bottom:1px solid #DFE1E6;flex-shrink:0">
                <div style="display:flex;gap:4px">
                    <span style="background:#FFFFFF;color:#42526E;border-radius:8px;padding:4px 11px;font-size:10px;font-family:ui-monospace,monospace;font-weight:700;letter-spacing:0.05em;border:1px solid #DFE1E6">TOP-DOWN</span>
                </div>
                <div style="width:1px;height:14px;background:#DFE1E6"></div>
                <span style="font-size:10px;color:#6B778C;font-family:ui-monospace,monospace">${Object.keys(_rfNodes).length} nodes · ${_rfEdges.length} edges</span>
                <div style="margin-left:auto;display:flex;gap:6px;align-items:center">
                    ${methodBadges}
                    <code style="font-size:11px;color:#6B778C;font-family:ui-monospace,monospace">/${_esc(route.uri||'')}</code>
                </div>
            </div>

            <!-- SVG scroll area -->
            <div style="flex:1;overflow:auto;padding:0;background:#F7F8F9" id="rf-canvas-wrap">
                <svg width="${CANVAS_W}" height="${CANVAS_H}" style="display:block;min-width:${CANVAS_W}px">
                    ${defs}
                    <rect width="${CANVAS_W}" height="${CANVAS_H}" fill="#F7F8F9"/>
                    <rect width="${CANVAS_W}" height="${CANVAS_H}" fill="url(#rf-dot)"/>
                    ${edgesSvg}
                    ${nodesSvg}
                </svg>
            </div>
        </div>

        <!-- ── Right panel ── -->
        <div style="width:256px;flex-shrink:0;background:#FFFFFF;border-left:1px solid #DFE1E6;display:flex;flex-direction:column;overflow:hidden">

            <!-- Route identity -->
            <div style="padding:14px 16px;border-bottom:1px solid #DFE1E6;background:#F4F5F7">
                <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;margin-bottom:4px">
                    ${methodBadges}
                </div>
                <code style="font-size:10px;color:#42526E;font-family:ui-monospace,monospace;word-break:break-all;display:block;margin-top:2px;font-weight:600">${_esc(route.uri||'')}</code>
                ${route.name ? `<p style="font-size:9px;color:#6B778C;font-family:ui-monospace,monospace;margin:2px 0 0">${_esc(route.name)}</p>` : ''}
            </div>

            <!-- Tabs -->
            <div style="display:flex;border-bottom:1px solid #DFE1E6;padding:0 4px;flex-shrink:0;background:#FFFFFF">
                <button id="rftab-info"  onclick="rfTab('info')"  style="flex:1;padding:8px 0;font-size:10px;color:#0052CC;background:none;border:none;border-bottom:2px solid #0052CC;cursor:pointer;font-family:inherit;font-weight:700;letter-spacing:0.04em">INFO</button>
                <button id="rftab-flow"  onclick="rfTab('flow')"  style="flex:1;padding:8px 0;font-size:10px;color:#6B778C;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;font-family:inherit;font-weight:600;letter-spacing:0.04em">FLOW</button>
                <button id="rftab-edges" onclick="rfTab('edges')" style="flex:1;padding:8px 0;font-size:10px;color:#6B778C;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;font-family:inherit;font-weight:600;letter-spacing:0.04em">EDGES</button>
            </div>

            <!-- Panel body -->
            <div id="rf-panel" style="flex:1;overflow-y:auto;padding:14px">
                ${rfNodeProps(firstNode)}
            </div>
        </div>

    </div>`;

    // Apply ATLAS dark theme to injected content
    const rdEl = document.getElementById('routes-detail-content');
    if (rdEl) _atlasTheme(rdEl);
    // Highlight first node
    setTimeout(() => rfHighlight(firstNode?.id), 10);
}

function rfProp(label, val) {
    if (!val && val !== 0) return '';
    return `<div style="margin-bottom:10px">
        <p style="font-size:8.5px;font-weight:700;color:#6B778C;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;font-family:ui-monospace,monospace">${label}</p>
        <p style="font-size:11px;color:#172B4D;font-family:ui-monospace,monospace;margin:0;word-break:break-all;line-height:1.4;font-weight:500">${val}</p>
    </div>`;
}

function rfNodeProps(node) {
    if (!node) return '';
    const route = _rfRoute, mws = _rfMws;
    const c = RF_COLOR[node.type] || RF_COLOR.unknown;
    const header = `<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid rgba(148,178,222,0.08)">
        <div style="width:8px;height:8px;border-radius:50%;background:${c.dot};flex-shrink:0"></div>
        <div>
            <p style="font-size:8px;color:${c.type};text-transform:uppercase;letter-spacing:0.1em;margin:0;font-family:ui-monospace,monospace;font-weight:700">${RF_TYPE_LABEL[node.type]||node.type}</p>
            <p style="font-size:11px;color:${c.name};font-family:ui-monospace,monospace;margin:0;font-weight:700;word-break:break-all">${_esc(node.name)}</p>
        </div>
    </div>`;

    const inferredBadge = node.meta?.inferred
        ? `<div style="margin-bottom:10px;padding:5px 8px;background:rgba(251,191,36,0.12);border:1px solid rgba(251,191,36,0.3);border-radius:6px;font-size:9px;color:#FBBF24;font-family:ui-monospace,monospace">~ inferred by naming convention</div>`
        : '';

    let body = inferredBadge;
    if (node.type === 'request') {
        const methods = (route.methods||[]).filter(m=>m!=='HEAD');
        body += rfProp('Method', methods.join(', ')) + rfProp('URI', '/' + (route.uri||'')) + rfProp('Route Name', route.name||null) + rfProp('Middleware', mws.length ? mws.length + ' layers' : 'None');
    } else if (node.type === 'middleware') {
        body += mws.map((mw, i) => rfProp('#' + (i+1), mw.split('\\').pop())).join('');
    } else if (node.type === 'controller') {
        const cc = route.controller || {};
        body += rfProp('Class', cc.class || node.name) + rfProp('Method', (node.meta?.action && node.meta.action !== '__invoke') ? node.meta.action : null) + rfProp('Type', (!node.meta?.action || node.meta.action === '__invoke') ? 'Invokable Controller' : 'Resource Method');
    } else if (node.type === 'model') {
        const md = (APP.models||[]).find(m => m.name === node.name);
        if (md) body += rfProp('Class', md.name) + rfProp('Table', md.table) + rfProp('Fillable', md.fillable?.length ? md.fillable.length + ' fields' : null) + rfProp('Relationships', md.relationships?.length ? md.relationships.length + ' defined' : null);
    } else if (node.type === 'database') {
        body += rfProp('Table', node.name) + rfProp('Model', node.meta?.model?.name || null);
    } else if (node.type === 'service') {
        const svc = (APP.services||[]).find(s => s.name === node.name);
        body += rfProp('Class', node.name) + rfProp('Methods', svc?.method_count ? svc.method_count + ' methods' : null);
    } else if (node.type === 'repository') {
        body += rfProp('Class', node.name) + rfProp('Layer', 'Repository Pattern');
    } else {
        body += rfProp('Name', node.name) + rfProp('Layer', node.type);
    }
    return header + body;
}

function rfFlowList() {
    return Object.values(_rfNodes).map(n => {
        const c = RF_COLOR[n.type] || RF_COLOR.unknown;
        return `<div onclick="rfClick('${n.id}')" style="display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:8px;cursor:pointer;margin-bottom:4px;border:1px solid #DFE1E6;background:#FFFFFF;transition:background 0.15s,box-shadow 0.15s" onmouseenter="this.style.boxShadow='0 2px 8px rgba(23,43,77,0.08)'" onmouseleave="this.style.boxShadow='none'">
            <div style="width:7px;height:7px;border-radius:50%;background:${c.dot};flex-shrink:0"></div>
            <div style="flex:1;min-width:0">
                <p style="font-size:10px;color:#172B4D;font-family:ui-monospace,monospace;font-weight:700;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${_esc(n.name)}</p>
                <p style="font-size:8px;color:${c.type};text-transform:uppercase;letter-spacing:0.08em;margin:0;font-family:ui-monospace,monospace;font-weight:600">${RF_TYPE_LABEL[n.type]||n.type}</p>
            </div>
        </div>`;
    }).join('');
}

function rfEdgeList() {
    return _rfEdges.map(e => {
        const from = _rfNodes[e.from], to = _rfNodes[e.to];
        if (!from || !to) return '';
        return `<div style="margin-bottom:8px;padding:8px;border-radius:8px;border:1px solid #DFE1E6;background:#FFFFFF">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                <span style="font-size:9px;color:#0052CC;font-family:ui-monospace,monospace;font-weight:700">${_esc(from.name)}</span>
                <span style="font-size:8px;color:#6B778C">→</span>
                <span style="font-size:9px;color:#6554C0;font-family:ui-monospace,monospace;font-weight:700">${_esc(to.name)}</span>
            </div>
            ${e.label ? `<span style="font-size:8px;color:#6B778C;font-family:ui-monospace,monospace;font-style:italic">${_esc(e.label)}</span>` : ''}
        </div>`;
    }).join('') || '<p style="font-size:11px;color:#6B778C;text-align:center;margin-top:20px">No edges</p>';
}

function rfClick(nid) {
    _rfSelected = _rfNodes[nid];
    rfHighlight(nid);
    rfRefreshPanel();
}

function rfHighlight(nid) {
    document.querySelectorAll('.rf-node rect:first-child').forEach(r => {
        r.setAttribute('stroke-width', '1.5');
        r.style.filter = '';
    });
    if (!nid) return;
    const rect = document.getElementById('rf-rect-' + nid);
    if (rect) { rect.setAttribute('stroke-width', '3'); rect.style.filter = 'brightness(1.5) drop-shadow(0 0 6px currentColor)'; }
}

function rfTab(tab) {
    _rfTab = tab;
    ['info','flow','edges'].forEach(t => {
        const btn = document.getElementById('rftab-' + t);
        if (!btn) return;
        const active = t === tab;
        btn.style.color        = active ? '#0052CC' : '#6B778C';
        btn.style.borderBottom = active ? '2px solid #0052CC' : '2px solid transparent';
    });
    rfRefreshPanel();
}

function rfRefreshPanel() {
    const el = document.getElementById('rf-panel');
    if (!el) return;
    if (_rfTab === 'info')  el.innerHTML = rfNodeProps(_rfSelected || Object.values(_rfNodes)[0]);
    if (_rfTab === 'flow')  el.innerHTML = rfFlowList();
    if (_rfTab === 'edges') el.innerHTML = rfEdgeList();
}

// ── Detail renderers ──────────────────────────────────────────────────────────

function renderDetail(type, item) {
    const map = {
        models: renderModel, controllers: renderController,
        jobs: renderJob, events: renderEvent,
        services: x => renderService(x, 'Service'),
        repositories: x => renderService(x, 'Repository'),
        observers: renderObserver, policies: renderPolicy,
    };
    return (map[type] || (() => ''))(item);
}

function detailCard(title, body) {
    return `<div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);padding:20px;margin-bottom:16px;"><h3 style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:12px;margin-top:0;">${title}</h3>${body}</div>`;
}

function pill(text, color) {
    const c = color || '#6B778C';
    return `<span style="font-size:11px;font-family:var(--font-mono);padding:2px 8px;border-radius:5px;background:rgba(142,155,184,.12);color:${c};border:1px solid rgba(142,155,184,.2);">${text}</span>`;
}

function avatar(letter, color) {
    const c = color || '#6B778C';
    const rgb = c.replace('#','').match(/.{2}/g).map(x=>parseInt(x,16)).join(',');
    return `<div style="width:48px;height:48px;border-radius:12px;background:rgba(${rgb},.15);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;color:${c};font-size:18px;font-weight:700;flex:none;">${letter}</div>`;
}

const MDS_PALETTE = [
    {color:'var(--cyan)',    rgb:'79,209,232',   hex:'#0052CC'},
    {color:'var(--violet)',  rgb:'167,139,250',  hex:'#A78BFA'},
    {color:'var(--emerald)', rgb:'52,211,153',   hex:'#34D399'},
    {color:'var(--amber)',   rgb:'251,191,36',   hex:'#FBBF24'},
    {color:'var(--rose)',    rgb:'248,113,113',  hex:'#F87171'},
    {color:'var(--sky)',     rgb:'96,165,250',   hex:'#60A5FA'},
];
const MDS_REL_CFG = {
    hasMany:       {hex:'#34D399',color:'var(--emerald)',bg:'rgba(52,211,153,.12)', border:'rgba(52,211,153,.3)'},
    hasOne:        {hex:'#0052CC',color:'var(--cyan)',   bg:'rgba(0,82,204,.12)', border:'rgba(0,82,204,.3)'},
    belongsTo:     {hex:'#60A5FA',color:'var(--sky)',    bg:'rgba(96,165,250,.12)', border:'rgba(96,165,250,.3)'},
    belongsToMany: {hex:'#A78BFA',color:'var(--violet)', bg:'rgba(167,139,250,.12)',border:'rgba(167,139,250,.3)'},
    morphMany:     {hex:'#F87171',color:'var(--rose)',   bg:'rgba(248,113,113,.12)',border:'rgba(248,113,113,.3)'},
    morphTo:       {hex:'#F87171',color:'var(--rose)',   bg:'rgba(248,113,113,.12)',border:'rgba(248,113,113,.3)'},
    morphOne:      {hex:'#F87171',color:'var(--rose)',   bg:'rgba(248,113,113,.12)',border:'rgba(248,113,113,.3)'},
    hasManyThrough:{hex:'#FBBF24',color:'var(--amber)',  bg:'rgba(251,191,36,.12)', border:'rgba(251,191,36,.3)'},
};

function _mdsColor(name) {
    const code = Math.abs((name || 'A').charCodeAt(0) - 65);
    return MDS_PALETTE[code % MDS_PALETTE.length];
}

function renderModel(m) {
    const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const pal = _mdsColor(m.name || 'A');

    // Find which controllers use this model via dep edges
    const depEdges = APP.dependencies?.edges || [];
    const usedBy = [];
    depEdges.forEach(e => {
        if (e.to === m.name || e.to === (m.name||'').split('\\').pop()) {
            const ctrl = (APP.controllers||[]).find(c => c.name === e.from);
            if (ctrl && !usedBy.find(u => u.name === ctrl.name)) {
                const rCnt = (APP.routes||[]).filter(r => (r.controller?.class||r.action||'').includes(ctrl.name)).length;
                usedBy.push({name:ctrl.name, routes:rCnt});
            }
        }
    });

    // Build unified field map from fillable + hidden + casts
    const fieldMap = new Map();
    (m.fillable||[]).forEach(f => fieldMap.set(f, {fillable:true, hidden:false, cast:null}));
    (m.hidden||[]).forEach(f => {
        const ex = fieldMap.get(f) || {fillable:false, hidden:false, cast:null};
        ex.hidden = true; fieldMap.set(f, ex);
    });
    Object.entries(m.casts||{}).forEach(([f,type]) => {
        const ex = fieldMap.get(f) || {fillable:false, hidden:false, cast:null};
        ex.cast = String(type); fieldMap.set(f, ex);
    });

    const fillCnt = m.fillable?.length || 0;
    const hideCnt = m.hidden?.length   || 0;
    const relCnt  = m.relationships?.length || 0;
    const castCnt = Object.keys(m.casts||{}).length;
    const traitCnt= m.traits?.length || 0;
    const initial = (m.name?.[0]||'?').toUpperCase();
    const traits  = (m.traits||[]).map(t => t.split('\\').pop());

    // ── 2-column wrapper ──
    let h = `<div class="mds-det-wrap">`;

    // ── LEFT SIDEBAR ──
    h += `<aside class="mds-sidebar">
      <div class="mds-side-card">
        <div class="mds-side-top">
          <div class="mds-side-av" style="background:rgba(${pal.rgb},.18);color:${pal.color};border-color:rgba(${pal.rgb},.4);">${esc(initial)}</div>
          <p class="mds-side-name">${esc(m.name)}</p>
          <p class="mds-side-tbl">
            <svg style="width:10px;height:10px;display:inline;margin-right:4px;vertical-align:middle;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0018 0V5"/></svg>
            ${esc(m.table)}
          </p>
          <p class="mds-side-ns">${esc(m.namespace||'')}</p>
        </div>

        <div class="mds-side-stats">
          ${fillCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Fillable</span><span class="mds-side-stat-val" style="color:${pal.color};">${fillCnt}</span></div>` : ''}
          ${hideCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Hidden</span><span class="mds-side-stat-val" style="color:var(--rose);">${hideCnt}</span></div>` : ''}
          ${castCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Casts</span><span class="mds-side-stat-val" style="color:var(--amber);">${castCnt}</span></div>` : ''}
          ${relCnt   ? `<div class="mds-side-stat" onclick="mdsTab('relations')" title="Go to relationships"><span class="mds-side-stat-lbl">Relationships</span><span class="mds-side-stat-val" style="color:var(--violet);">${relCnt}</span></div>` : ''}
          ${usedBy.length ? `<div class="mds-side-stat" onclick="mdsTab('usedby')" title="Go to used by"><span class="mds-side-stat-lbl">Used by</span><span class="mds-side-stat-val" style="color:var(--sky);">${usedBy.length}</span></div>` : ''}
          ${!fillCnt && !relCnt && !usedBy.length ? `<p style="font-size:12px;color:var(--text-faint);text-align:center;padding:8px 0;">No data available</p>` : ''}
        </div>

        <div class="mds-side-meta">
          ${m.observer ? `<p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Observer</p>
          <span class="mds-side-chip" style="background:rgba(251,191,36,.1);color:var(--amber);border-color:rgba(251,191,36,.25);">${esc(m.observer.split('\\').pop())}</span>` : ''}
          ${traits.length ? `<p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin:${m.observer?'12':'0'}px 0 8px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Traits</p>
          <div style="display:flex;flex-wrap:wrap;gap:5px;">${traits.map(t => `<span class="mds-side-chip" style="background:rgba(167,139,250,.1);color:var(--violet);border-color:rgba(167,139,250,.25);">${esc(t)}</span>`).join('')}</div>` : ''}
          ${m.timestamps !== false ? `<span class="mds-side-chip" style="background:rgba(96,165,250,.08);color:var(--sky);border-color:rgba(96,165,250,.2);margin-top:8px;">timestamps</span>` : ''}
        </div>
      </div>
    </aside>`;

    // ── RIGHT MAIN CONTENT ──
    h += `<div>`;

    // Tabs
    h += `<div class="mds-tabs" id="mds-tabs-row">
      <button class="mds-tab-btn active" id="mds-tab-fields"    onclick="mdsTab('fields')">
        Fields ${fieldMap.size ? `<span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(${pal.rgb},.15);color:${pal.color};margin-left:5px;font-family:var(--font-mono);">${fieldMap.size}</span>` : ''}
      </button>
      ${relCnt ? `<button class="mds-tab-btn" id="mds-tab-relations" onclick="mdsTab('relations')">Relationships <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(167,139,250,.15);color:var(--violet);margin-left:5px;font-family:var(--font-mono);">${relCnt}</span></button>` : ''}
      ${usedBy.length ? `<button class="mds-tab-btn" id="mds-tab-usedby" onclick="mdsTab('usedby')">Used By <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(0,82,204,.15);color:var(--cyan);margin-left:5px;font-family:var(--font-mono);">${usedBy.length}</span></button>` : ''}
    </div>`;

    // ── Fields Tab ──
    h += `<div class="mds-tab-pane active" id="mds-pane-fields">`;
    if (fieldMap.size) {
        h += `<div class="mds-schema-wrap">
          <table class="mds-schema-tbl">
            <thead><tr>
              <th>Field</th><th>Status</th><th>Cast Type</th>
            </tr></thead>
            <tbody>`;
        fieldMap.forEach((info, fname) => {
            h += `<tr>
              <td><span class="mds-field-name">${esc(fname)}</span></td>
              <td>
                ${info.fillable ? `<span class="mds-fbadge fill">FILLABLE</span>` : ''}
                ${info.hidden   ? `<span class="mds-fbadge hide">HIDDEN</span>`   : ''}
              </td>
              <td>${info.cast ? `<span class="mds-cast-val">${esc(info.cast)}</span>` : '<span style="color:var(--text-faint);font-size:12px;">—</span>'}</td>
            </tr>`;
        });
        h += `</tbody></table></div>`;
    }

    // Feature flags
    const flags = [];
    if (traits.some(t => t.includes('SoftDeletes'))) flags.push({label:'SoftDeletes', color:'var(--rose)',    bg:'rgba(248,113,113,.1)', border:'rgba(248,113,113,.2)'});
    if (traits.some(t => t.includes('HasFactory')))  flags.push({label:'HasFactory',  color:'var(--emerald)', bg:'rgba(52,211,153,.1)',  border:'rgba(52,211,153,.2)'});
    if (traits.some(t => t.includes('Searchable')))  flags.push({label:'Searchable',  color:'var(--sky)',     bg:'rgba(96,165,250,.1)',  border:'rgba(96,165,250,.2)'});
    if (m.timestamps !== false)                       flags.push({label:'$timestamps = true', color:'var(--sky)', bg:'rgba(96,165,250,.08)', border:'rgba(96,165,250,.15)'});
    if (flags.length) {
        h += `<div class="mds-flag-row">${flags.map(f => `<span class="mds-flag" style="color:${f.color};background:${f.bg};border-color:${f.border};">${esc(f.label)}</span>`).join('')}</div>`;
    }
    if (!fieldMap.size && !flags.length) {
        h += `<p style="color:var(--text-faint);font-size:13px;text-align:center;padding:40px 0;">No fillable, hidden or cast fields detected.</p>`;
    }
    h += `</div>`; // end fields pane

    // ── Relationships Tab ──
    if (relCnt) {
        h += `<div class="mds-tab-pane" id="mds-pane-relations">`;
        (m.relationships||[]).forEach(r => {
            const rc  = MDS_REL_CFG[r.type] || {color:'var(--text-dim)',bg:'rgba(91,103,133,.1)',border:'var(--border)'};
            const rel = r.related ? r.related.split('\\').pop() : '—';
            const navIdx = (APP.models||[]).findIndex(md => md.name === rel);
            h += `<div class="mds-rel-card" style="border-color:var(--border);" onmouseenter="this.style.borderColor='${rc.border}'" onmouseleave="this.style.borderColor='var(--border)'">
              <span class="mds-rel-method">${esc(r.method)}()</span>
              <span class="mds-rel-type" style="color:${rc.color};background:${rc.bg};border-color:${rc.border};">${esc(r.type)}</span>
              <span class="mds-rel-arrow">→</span>
              <span class="mds-rel-target">${esc(rel)}</span>
              ${navIdx >= 0
                ? `<button class="mds-nav-btn" style="color:${rc.color};background:${rc.bg};border-color:${rc.border};" onclick="event.stopPropagation();showDetail('models',${navIdx});">View →</button>`
                : '<span></span>'}
            </div>`;
        });
        h += `</div>`;
    }

    // ── Used By Tab ──
    if (usedBy.length) {
        h += `<div class="mds-tab-pane" id="mds-pane-usedby">`;
        usedBy.forEach(u => {
            const ci = (APP.controllers||[]).findIndex(c => c.name === u.name);
            h += `<div class="mds-usedby-card">
              <div>
                <span style="font-size:14px;font-weight:700;color:var(--text);">${esc(u.name)}</span>
                <span style="font-size:11px;color:var(--text-faint);margin-left:10px;font-family:var(--font-mono);">${u.routes} route${u.routes!==1?'s':''}</span>
              </div>
              ${ci >= 0 ? `<button class="mds-nav-btn" style="color:var(--sky);background:rgba(96,165,250,.08);border-color:rgba(96,165,250,.2);" onclick="event.stopPropagation();navigate('controllers');setTimeout(()=>showDetail('controllers',${ci}),100);">View controller →</button>` : ''}
            </div>`;
        });
        h += `</div>`;
    }

    h += `</div></div>`; // close main + wrap
    return h;
}

function mdsTab(tab) {
    document.querySelectorAll('.mds-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.mds-tab-pane').forEach(p => p.classList.remove('active'));
    const btn  = document.getElementById('mds-tab-' + tab);
    const pane = document.getElementById('mds-pane-' + tab);
    if (btn)  btn.classList.add('active');
    if (pane) pane.classList.add('active');
}

function mdsView(view) {
    document.getElementById('mds-grid-view').style.display = view === 'grid' ? '' : 'none';
    document.getElementById('mds-list-view').style.display = view === 'list' ? '' : 'none';
    document.getElementById('mds-vbtn-grid').classList.toggle('active', view === 'grid');
    document.getElementById('mds-vbtn-list').classList.toggle('active', view === 'list');
}

function renderController(c) {
    // Sanitize a string for safe HTML injection (XSS prevention)
    const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    // Sanitize a string for safe use inside Mermaid node labels (strip quotes/brackets/newlines)
    const mEsc = s => String(s ?? '').replace(/["[\]\n\r]/g, '').replace(/&/g, 'and').trim();
    // Safe first-letter avatar — never undefined
    const initial = s => (String(s || '?')[0] || '?').toUpperCase();

    const RESOURCE_VERBS = {
        index:   {verb:'GET',         color:'method-get'},
        create:  {verb:'GET',         color:'method-get'},
        store:   {verb:'POST',        color:'method-post'},
        show:    {verb:'GET',         color:'method-get'},
        edit:    {verb:'GET',         color:'method-get'},
        update:  {verb:'PUT / PATCH', color:'method-patch'},
        destroy: {verb:'DELETE',      color:'method-delete'},
    };

    // ── linked routes for this controller ─────────────────────────
    const ctrlBase = c.name;
    const linkedRoutes = (APP.routes || []).filter(r => {
        const cls = (r.controller?.class || r.action || '');
        return cls.split('\\').pop() === ctrlBase || cls === ctrlBase;
    });

    // ── dependency graph nodes from dep analyser ───────────────────
    const depEdges = (APP.dependencies?.edges || []).filter(e => e.from === ctrlBase);
    const usedModels   = depEdges.filter(e => e.type === 'uses' && (APP.dependencies?.nodes||[]).find(n=>n.name===e.to&&n.layer==='model')).map(e=>e.to);
    const usedServices = depEdges.filter(e => e.type === 'injects' || e.type === 'uses').map(e=>e.to).filter(t=>!usedModels.includes(t));

    // ── Header ─────────────────────────────────────────────────────
    let h = `
    <div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:14px;background:rgba(96,165,250,.15);border:1px solid rgba(96,165,250,.3);display:flex;align-items:center;justify-content:center;color:#60A5FA;font-weight:700;font-size:22px;flex:none;">${esc(initial(c.name))}</div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <h2 style="font-size:22px;font-weight:700;color:var(--text);margin:0;">${esc(c.name)}</h2>
                    ${c.is_resource ? '<span style="font-size:11px;background:rgba(52,211,153,.12);color:#34D399;border:1px solid rgba(52,211,153,.3);padding:2px 10px;border-radius:20px;font-weight:600;">Resource Controller</span>' : ''}
                </div>
                <p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(c.namespace)}&#92;${esc(c.name)}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin:0;">${esc(c.path || '')}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
            <div style="background:rgba(96,165,250,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(96,165,250,.18);">
                <p style="font-size:22px;font-weight:700;color:#60A5FA;margin:0 0 2px;">${c.method_count || 0}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Methods</p>
            </div>
            <div style="background:rgba(167,139,250,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(167,139,250,.18);">
                <p style="font-size:22px;font-weight:700;color:#A78BFA;margin:0 0 2px;">${(c.dependencies||[]).length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Dependencies</p>
            </div>
            <div style="background:rgba(0,82,204,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(0,82,204,.18);">
                <p style="font-size:22px;font-weight:700;color:#0052CC;margin:0 0 2px;">${linkedRoutes.length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Routes</p>
            </div>
            <div style="background:rgba(52,211,153,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(52,211,153,.18);">
                <p style="font-size:22px;font-weight:700;color:#34D399;margin:0 0 2px;">${usedModels.length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Models Used</p>
            </div>
        </div>
    </div>`;

    // ── Request Flow Diagram ───────────────────────────────────────
    const flowId = 'ctrl-flow-' + c.name.replace(/\W/g,'');
    let flowLines = ['flowchart LR'];

    // Route node(s) — sanitize URIs for Mermaid labels
    if (linkedRoutes.length > 0) {
        const routeLabels = linkedRoutes.slice(0,3).map(r => {
            const ms = (r.methods||[]).filter(m=>m!=='HEAD');
            return mEsc((ms[0]||'?') + ' ' + (r.uri||''));
        });
        flowLines.push(`    Route["🌐 Route\\n${routeLabels.join('\\n')}"]`);
    } else {
        flowLines.push(`    Route["🌐 Route"]`);
    }

    // Middleware — sanitize labels for Mermaid
    const mwList = c.middleware || [];
    const routeMw = linkedRoutes.length > 0
        ? [...new Set(linkedRoutes.flatMap(r => r.middleware || []))]
        : [];
    const allMw = [...new Set([...mwList, ...routeMw])].filter(Boolean);
    if (allMw.length > 0) {
        flowLines.push(`    Middleware["🔒 Middleware\\n${allMw.slice(0,3).map(mEsc).join('\\n')}"]`);
        flowLines.push(`    Route --> Middleware`);
        flowLines.push(`    Middleware --> Controller["🎮 ${mEsc(c.name)}"]`);
    } else {
        flowLines.push(`    Route --> Controller["🎮 ${mEsc(c.name)}"]`);
    }

    // Dependencies (services/repos) — sanitize type names
    const deps = c.dependencies || [];
    deps.forEach((dep, i) => {
        const nodeId = 'Dep' + i;
        flowLines.push(`    ${nodeId}["⚙️ ${mEsc(dep.type)}"]`);
        flowLines.push(`    Controller --> ${nodeId}`);
    });

    // Models — sanitize model names
    usedModels.forEach((m, i) => {
        const nodeId = 'Mdl' + i;
        flowLines.push(`    ${nodeId}["📦 ${mEsc(m)}"]`);
        const parentId = deps.length > 0 ? 'Dep0' : 'Controller';
        flowLines.push(`    ${parentId} --> ${nodeId}`);
        flowLines.push(`    ${nodeId} --> DB[(Database)]`);
    });
    if (usedModels.length === 0 && deps.length === 0) {
        flowLines.push(`    Controller --> DB[(Database)]`);
    }

    // Styles — ATLAS dark theme
    flowLines.push(`    classDef ctrl fill:#EAF2FF,stroke:#0052CC,color:#172B4D`);
    flowLines.push(`    classDef mw  fill:#FFFAE6,stroke:#FF8B00,color:#172B4D`);
    flowLines.push(`    classDef dep fill:#F3F0FF,stroke:#6554C0,color:#172B4D`);
    flowLines.push(`    classDef mdl fill:#E3FCEF,stroke:#00875A,color:#172B4D`);
    flowLines.push(`    classDef db  fill:#F4F5F7,stroke:#6B778C,color:#172B4D`);
    flowLines.push(`    classDef rt  fill:#EAF2FF,stroke:#0052CC,color:#172B4D`);
    flowLines.push(`    class Controller ctrl`);
    flowLines.push(`    class Route rt`);
    if (allMw.length > 0) flowLines.push(`    class Middleware mw`);
    deps.forEach((_, i) => flowLines.push(`    class Dep${i} dep`));
    usedModels.forEach((_, i) => flowLines.push(`    class Mdl${i} mdl`));
    if (usedModels.length > 0 || deps.length === 0) flowLines.push(`    class DB db`);

    h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Request Flow
        </h3>
        <div class="mermaid" id="${esc(flowId)}">${flowLines.join('\n')}</div>
    </div>`;

    // ── Methods Table ──────────────────────────────────────────────
    if ((c.method_details || c.methods || []).length) {
        const methods = c.method_details || (c.methods||[]).map(m=>({method:m, params:[]}));
        const RESOURCE_SET = new Set(['index','create','store','show','edit','update','destroy']);

        let rows = methods.map(md => {
            const mName  = md.method || md;
            const params = md.params || [];
            const rv     = RESOURCE_VERBS[mName];
            const isRes  = RESOURCE_SET.has(mName);

            // Find matching route(s)
            const matchedRoutes = linkedRoutes.filter(r => {
                const act = r.controller?.method || r.action || '';
                return act === mName || act.split('@')[1] === mName;
            });

            const httpBadge = rv
                ? `<span class="text-xs font-bold px-1.5 py-0.5 rounded ${rv.color}">${esc(rv.verb)}</span>`
                : (matchedRoutes.length > 0
                    ? matchedRoutes.slice(0,2).map(r => {
                        const ms = (r.methods||[]).filter(m=>m!=='HEAD');
                        return `<span class="text-xs font-bold px-1.5 py-0.5 rounded method-${esc((ms[0]||'get').toLowerCase())}">${esc(ms[0]||'?')}</span>`;
                      }).join(' ')
                    : `<span class="text-xs text-slate-300 italic">—</span>`);

            const routeUri = matchedRoutes.length > 0
                ? `<span class="text-xs text-slate-400 font-mono">${esc(matchedRoutes[0].uri||'')}</span>`
                : '';

            const paramBadges = params.length
                ? params.map(p => `<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-mono">${p.type ? esc(p.type) + ' ' : ''}<span class="text-indigo-500">$${esc(p.var)}</span></span>`).join(' ')
                : '<span class="text-xs text-slate-300 italic">no params</span>';

            const mwOnMethod = matchedRoutes.length > 0
                ? [...new Set(matchedRoutes.flatMap(r => r.middleware||[]))].slice(0,2)
                    .map(m => `<span class="text-xs bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded">${esc(m)}</span>`).join(' ')
                : '';

            return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-semibold text-slate-700 text-sm">${esc(mName)}</span>
                        ${isRes ? '<span class="text-xs bg-blue-50 text-blue-500 px-1.5 py-0.5 rounded">resource</span>' : ''}
                    </div>
                </td>
                <td class="py-3 px-4">${httpBadge}</td>
                <td class="py-3 px-4">${routeUri}</td>
                <td class="py-3 px-4"><div class="flex flex-wrap gap-1">${paramBadges}</div></td>
                <td class="py-3 px-4"><div class="flex flex-wrap gap-1">${mwOnMethod}</div></td>
            </tr>`;
        }).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Methods (${methods.length})</h3>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100 bg-slate-50">
                    <th class="text-left py-2.5 px-4 font-medium">Method</th>
                    <th class="text-left py-2.5 px-4 font-medium">HTTP</th>
                    <th class="text-left py-2.5 px-4 font-medium">Route URI</th>
                    <th class="text-left py-2.5 px-4 font-medium">Parameters</th>
                    <th class="text-left py-2.5 px-4 font-medium">Middleware</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>
            </div>
        </div>`;
    }

    // ── Dependencies Section ───────────────────────────────────────
    if ((c.dependencies||[]).length) {
        const depCards = c.dependencies.map(d => `
            <div class="flex items-center gap-3 p-3 bg-purple-50 border border-purple-100 rounded-xl">
                <div class="w-8 h-8 rounded-lg bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-sm shrink-0">${esc(initial(d.type))}</div>
                <div>
                    <p class="font-semibold text-slate-700 text-sm">${esc(d.type)}</p>
                    <p class="text-xs text-slate-400 font-mono">$${esc(d.var)}</p>
                </div>
            </div>`).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Constructor Dependencies (${c.dependencies.length})</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">${depCards}</div>
        </div>`;
    }

    // ── Linked Routes ──────────────────────────────────────────────
    if (linkedRoutes.length) {
        const routeRows = linkedRoutes.map(r => {
            const ms     = (r.methods||[]).filter(m=>m!=='HEAD');
            const method = ms[0] || '?';
            const mwBadges = (r.middleware||[]).slice(0,3).map(m =>
                `<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">${esc(m)}</span>`).join(' ');
            const name = r.name ? `<span class="text-xs text-slate-400 font-mono">${esc(r.name)}</span>` : '';
            return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4"><span class="text-xs font-bold px-1.5 py-0.5 rounded method-${esc(method.toLowerCase())}">${esc(method)}</span></td>
                <td class="py-3 px-4 font-mono text-sm text-slate-700">${esc(r.uri||'')}</td>
                <td class="py-3 px-4 font-mono text-xs text-slate-400">${esc(r.controller?.method || '')}</td>
                <td class="py-3 px-4">${name}</td>
                <td class="py-3 px-4"><div class="flex gap-1 flex-wrap">${mwBadges}</div></td>
            </tr>`;
        }).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Linked Routes (${linkedRoutes.length})</h3>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100 bg-slate-50">
                    <th class="text-left py-2.5 px-4 font-medium">Method</th>
                    <th class="text-left py-2.5 px-4 font-medium">URI</th>
                    <th class="text-left py-2.5 px-4 font-medium">Action</th>
                    <th class="text-left py-2.5 px-4 font-medium">Name</th>
                    <th class="text-left py-2.5 px-4 font-medium">Middleware</th>
                </tr></thead>
                <tbody>${routeRows}</tbody>
            </table>
            </div>
        </div>`;
    }

    // ── Used Models & Services ─────────────────────────────────────
    if (usedModels.length || usedServices.length) {
        let grid = '';
        usedModels.forEach(m => {
            grid += `<div class="flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                <div class="w-7 h-7 rounded-lg bg-emerald-200 flex items-center justify-center text-emerald-700 font-bold text-xs">${esc(initial(m))}</div>
                <div><p class="text-sm font-semibold text-slate-700">${esc(m)}</p><p class="text-xs text-emerald-500">Model</p></div>
            </div>`;
        });
        usedServices.forEach(s => {
            grid += `<div class="flex items-center gap-2 p-3 bg-violet-50 border border-violet-100 rounded-xl">
                <div class="w-7 h-7 rounded-lg bg-violet-200 flex items-center justify-center text-violet-700 font-bold text-xs">${esc(initial(s))}</div>
                <div><p class="text-sm font-semibold text-slate-700">${esc(s)}</p><p class="text-xs text-violet-500">Service / Repo</p></div>
            </div>`;
        });

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Used Models & Services</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">${grid}</div>
        </div>`;
    }

    // Render only the newly created flowchart (not all .mermaid elements)
    setTimeout(() => {
        if (window.mermaid) {
            const flowEl = document.getElementById(flowId);
            if (flowEl && !flowEl.dataset.processed) {
                try { mermaid.run({ nodes: [flowEl] }); } catch(e){}
            }
        }
    }, 50);

    return h;
}

function renderJob(j) {
    let h = `<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">${avatar(j.name[0],'#FBBF24')}
        <div><h2 style="font-size:20px;font-weight:700;color:var(--text);margin:0 0 2px;">${j.name}</h2><p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0;">${j.namespace}</p></div></div>`;
    let meta = `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px;font-size:13px;">
        <div><p style="font-size:11px;color:var(--text-faint);margin:0 0 4px;">Queue</p><p style="font-weight:500;color:var(--text);margin:0;">${j.queue || 'default'}</p></div>`;
    if (j.tries)   meta += `<div><p style="font-size:11px;color:var(--text-faint);margin:0 0 4px;">Tries</p><p style="font-weight:500;color:var(--text);margin:0;">${j.tries}</p></div>`;
    if (j.timeout) meta += `<div><p style="font-size:11px;color:var(--text-faint);margin:0 0 4px;">Timeout</p><p style="font-weight:500;color:var(--text);margin:0;">${j.timeout}s</p></div>`;
    if (j.delay)   meta += `<div><p style="font-size:11px;color:var(--text-faint);margin:0 0 4px;">Delay</p><p style="font-weight:500;color:var(--text);margin:0;">${j.delay}s</p></div>`;
    meta += '</div>';
    const flags = [j.queued && pill('ShouldQueue','#FBBF24'), j.unique && pill('ShouldBeUnique','#0052CC'), j.encrypted && pill('ShouldBeEncrypted','#A78BFA')].filter(Boolean);
    if (flags.length) meta += `<div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">${flags.join('')}</div>`;
    h += detailCard('Queue Config', meta);
    if (j.dependencies?.length) h += detailCard('Dependencies', `<div style="display:flex;flex-wrap:wrap;gap:6px;">${j.dependencies.map(d => pill(d.split('\\\\').pop())).join('')}</div>`);
    return h;
}

function renderEvent(e) {
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(e.name[0],'bg-pink-100','text-pink-600')}
        <div><h2 class="text-xl font-bold">${e.name}</h2><p class="text-sm text-slate-400 font-mono">${e.namespace}</p></div></div>`;
    const flags = [e.broadcasts && pill('ShouldBroadcast','bg-pink-50 text-pink-700'), e.broadcastNow && pill('ShouldBroadcastNow','bg-rose-50 text-rose-700')].filter(Boolean);
    if (flags.length) h += detailCard('Broadcast', `<div class="flex gap-2">${flags.join('')}</div>`);
    if (e.properties?.length) h += detailCard('Payload Properties', `<div class="flex flex-wrap gap-2">${e.properties.map(p => pill(p)).join('')}</div>`);
    return h;
}

function renderService(s, type) {
    const [bg, fg] = type === 'Repository' ? ['bg-cyan-100','text-cyan-600'] : ['bg-purple-100','text-purple-600'];
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(s.name[0], bg, fg)}
        <div><h2 class="text-xl font-bold">${s.name}</h2><p class="text-sm text-slate-400 font-mono">${s.namespace}</p></div></div>`;
    if (s.dependencies?.length) h += detailCard('Dependencies', `<div class="flex flex-wrap gap-2">${s.dependencies.map(d => pill(d.split('\\').pop())).join('')}</div>`);
    if (s.methods?.length) h += detailCard(`Public Methods (${s.methods.length})`, `<div class="flex flex-wrap gap-2">${s.methods.map(m => pill(m, 'bg-slate-100 text-slate-700')).join('')}</div>`);
    return h;
}

function renderObserver(o) {
    const colors = {created:'bg-green-50 text-green-700',updated:'bg-blue-50 text-blue-700',deleted:'bg-red-50 text-red-700',saved:'bg-teal-50 text-teal-700',creating:'bg-emerald-50 text-emerald-700',updating:'bg-sky-50 text-sky-700',deleting:'bg-rose-50 text-rose-700',saving:'bg-cyan-50 text-cyan-700'};
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(o.name[0],'bg-orange-100','text-orange-600')}
        <div><h2 class="text-xl font-bold">${o.name}</h2><p class="text-sm text-slate-400 font-mono">${o.namespace}</p></div></div>`;
    h += detailCard('Observes', `<p class="font-medium text-slate-700">${o.observes || 'Unknown'}</p>`);
    if (o.events?.length) h += detailCard('Lifecycle Events', `<div class="flex flex-wrap gap-2">${o.events.map(e => pill(e, colors[e] || 'bg-slate-100 text-slate-600')).join('')}</div>`);
    return h;
}

function renderPolicy(p) {
    const colors = {viewAny:'bg-blue-50 text-blue-700',view:'bg-sky-50 text-sky-700',create:'bg-green-50 text-green-700',update:'bg-yellow-50 text-yellow-700',delete:'bg-red-50 text-red-700',restore:'bg-teal-50 text-teal-700',forceDelete:'bg-rose-50 text-rose-700',before:'bg-purple-50 text-purple-700'};
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(p.name[0],'bg-slate-200','text-slate-600')}
        <div><h2 class="text-xl font-bold">${p.name}</h2><p class="text-sm text-slate-400 font-mono">${p.namespace}</p></div></div>`;
    h += detailCard('Guards Model', `<p class="font-medium text-slate-700">${p.model || 'Unknown'}</p>`);
    if (p.actions?.length) h += detailCard('Policy Actions', `<div class="flex flex-wrap gap-2">${p.actions.map(a => pill(a, colors[a] || 'bg-slate-100 text-slate-600')).join('')}</div>`);
    return h;
}

// ── Model Relationship Map ────────────────────────────────────────────────────

const REL_COLORS = {
    hasMany:        { color:'#34D399', bg:'rgba(52,211,153,.13)',  border:'rgba(52,211,153,.3)'  },
    hasOne:         { color:'#0052CC', bg:'rgba(0,82,204,.13)',  border:'rgba(0,82,204,.3)'  },
    belongsTo:      { color:'#60A5FA', bg:'rgba(96,165,250,.13)',  border:'rgba(96,165,250,.3)'  },
    belongsToMany:  { color:'#A78BFA', bg:'rgba(167,139,250,.13)', border:'rgba(167,139,250,.3)' },
    morphMany:      { color:'#FB923C', bg:'rgba(251,146,60,.13)',  border:'rgba(251,146,60,.3)'  },
    morphOne:       { color:'#FBBF24', bg:'rgba(251,191,36,.13)',  border:'rgba(251,191,36,.3)'  },
    morphTo:        { color:'#F87171', bg:'rgba(248,113,113,.13)', border:'rgba(248,113,113,.3)' },
    morphToMany:    { color:'#E879F9', bg:'rgba(232,121,249,.13)', border:'rgba(232,121,249,.3)' },
    hasManyThrough: { color:'#2DD4BF', bg:'rgba(45,212,191,.13)',  border:'rgba(45,212,191,.3)'  },
    hasOneThrough:  { color:'#38BDF8', bg:'rgba(56,189,248,.13)',  border:'rgba(56,189,248,.3)'  },
};

function buildRelBranch(modelName, depth, visited) {
    if (depth > 2 || visited.has(modelName)) return '';
    const model = (APP.models || []).find(m => m.name === modelName);
    if (!model) return '';

    const rels = model.relationships || [];
    if (rels.length === 0) return '';

    const newVis = new Set(visited);
    newVis.add(modelName);

    let html = `<div style="padding-left:16px;border-left:1px solid rgba(148,178,222,0.18);margin-top:2px;">`;

    rels.forEach((rel, i) => {
        const relName = rel.related ? rel.related.split('\\').pop() : null;
        const cfg     = REL_COLORS[rel.type] || { color:'#6B778C', bg:'rgba(142,155,184,.1)', border:'rgba(142,155,184,.25)' };
        const isLast  = i === rels.length - 1;
        const isCirc  = relName && newVis.has(relName);
        const hasSub  = relName && depth < 1 && !newVis.has(relName) &&
                        (APP.models || []).some(m => m.name === relName && (m.relationships||[]).length > 0);

        html += `<div style="display:flex;align-items:flex-start;gap:0;padding:3px 0;">
            <span style="color:rgba(148,178,222,0.3);font-family:var(--font-mono);font-size:11px;padding-top:2px;margin-right:8px;flex:none;user-select:none;">${isLast ? '└─' : '├─'}</span>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
                    <span style="font-family:var(--font-mono);font-size:10px;padding:1px 6px;border-radius:4px;border:1px solid ${cfg.border};background:${cfg.bg};color:${cfg.color};flex:none;">${rel.type}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--text);">${relName || '<em style="color:var(--text-faint);font-style:italic;">unknown</em>'}</span>
                    ${rel.method ? `<span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">.${rel.method}()</span>` : ''}
                    ${isCirc ? '<span style="font-size:10px;color:var(--rose);">↩ circular</span>' : ''}
                </div>
                ${hasSub ? buildRelBranch(relName, depth + 1, newVis) : ''}
            </div>
        </div>`;
    });

    html += '</div>';
    return html;
}

function renderModelTree() {
    const search    = (document.getElementById('map-search')?.value || '').toLowerCase();
    const allModels = APP.models || [];
    const models    = allModels.filter(m => !search || m.name.toLowerCase().includes(search));
    const container = document.getElementById('map-tree-content');

    if (!models.length) {
        container.innerHTML = '<p style="color:var(--text-faint);font-size:13px;padding:20px 0;">No models match your search.</p>';
        return;
    }

    // Sort: models with rels first, then alphabetically
    const sorted = [...models].sort((a, b) => {
        const da = (b.relationships||[]).length - (a.relationships||[]).length;
        return da !== 0 ? da : a.name.localeCompare(b.name);
    });

    const PALETTE = ['#0052CC','#A78BFA','#34D399','#FBBF24','#F87171','#60A5FA'];

    let html = `<div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;">`;

    sorted.forEach((model, idx) => {
        const rels    = model.relationships || [];
        const total   = rels.length;
        const color   = PALETTE[idx % PALETTE.length];
        const domId   = 'tv-body-' + idx;
        const isLast  = idx === sorted.length - 1;
        const hasRels = total > 0;

        html += `
        <div class="model-tree-card" data-name="${model.name.toLowerCase()}"
             style="border-bottom:${isLast ? 'none' : '1px solid var(--border)'};">

            <div onclick="tvToggle('${domId}','${domId}-arrow',${hasRels})"
                 style="display:flex;align-items:center;gap:12px;padding:12px 18px;cursor:${hasRels ? 'pointer' : 'default'};transition:background .18s;user-select:none;"
                 onmouseenter="if(${hasRels})this.style.background='rgba(255,255,255,.03)'" onmouseleave="this.style.background=''">

                <svg id="${domId}-arrow" viewBox="0 0 10 10" style="width:10px;height:10px;flex:none;transition:transform .2s;opacity:${hasRels ? '1' : '.2'};" fill="currentColor" color="var(--text-faint)">
                    <path d="M3 2l4 3-4 3z"/>
                </svg>

                <div style="width:32px;height:32px;border-radius:9px;flex:none;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;background:${color}18;color:${color};border:1px solid ${color}35;">
                    ${model.name[0]}
                </div>

                <span style="font-size:13.5px;font-weight:700;color:var(--text);flex:1;">${model.name}</span>
                <code style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);margin-right:4px;">${model.table || ''}</code>

                ${model.observer ? '<span style="font-size:9px;padding:1px 5px;border-radius:4px;background:rgba(251,191,36,.12);color:var(--amber);border:1px solid rgba(251,191,36,.2);font-family:var(--font-mono);flex:none;">obs</span>' : ''}

                ${hasRels
                    ? `<span style="font-family:var(--font-mono);font-size:10px;padding:2px 8px;border-radius:10px;background:${color}14;color:${color};border:1px solid ${color}30;flex:none;margin-left:6px;">${total} rel${total !== 1 ? 's' : ''}</span>`
                    : `<span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);opacity:.45;margin-left:6px;">no rels</span>`
                }
            </div>

            <div id="${domId}" style="display:none;padding:0 18px 14px 56px;">
                ${hasRels ? buildRelBranch(model.name, 0, new Set()) : ''}
            </div>
        </div>`;
    });

    html += '</div>';
    container.innerHTML = html;
}

function tvToggle(bodyId, arrowId, hasRels) {
    if (!hasRels) return;
    const body  = document.getElementById(bodyId);
    const arrow = document.getElementById(arrowId);
    if (!body) return;
    const open = body.style.display !== 'none';
    body.style.display  = open ? 'none' : 'block';
    arrow.style.transform = open ? '' : 'rotate(90deg)';
}

function filterModelTree() {
    if (!mapTreeRendered) return;
    renderModelTree();
}

function setMapTab(tab) {
    ['graph','tree','er'].forEach(t => {
        document.getElementById('map-' + t).style.display = t === tab ? 'block' : 'none';
        const btn = document.getElementById('map-tab-' + t);
        if (btn) {
            btn.style.background    = t === tab ? 'var(--bg-elevated)' : 'none';
            btn.style.color         = t === tab ? 'var(--text)'        : 'var(--text-dim)';
            btn.style.borderColor   = t === tab ? 'var(--border)'      : 'transparent';
        }
    });

    if (tab === 'graph' && !graphRendered) {
        setTimeout(initRelGraph, 50);
    }
    if (tab === 'tree' && !mapTreeRendered) {
        mapTreeRendered = true;
        renderModelTree();
    }
    if (tab === 'er' && !erRendered) {
        erRendered = true;
        const modelCount = (APP.models || []).length;
        if (modelCount > erLargeThreshold) {
            // Auto-focus on first model that has relationships
            const first = (APP.models || []).find(m => (m.relationships || []).length > 0);
            if (first) {
                const sel = document.getElementById('er-focus-select');
                if (sel) sel.value = first.name;
                erFocus(first.name);
                return;
            }
        }
        const el = document.getElementById('er-diagram');
        if (el && typeof mermaid !== 'undefined') mermaid.run({ nodes: [el] });
    }
}


// ── Relation Graph (force-directed SVG) ───────────────────────────────────────

const RG_NW = 150, RG_NH = 60;
let _rgNodes = [], _rgSel = null, _rgAdj = {};
let _rgVp = { x: 0, y: 0, z: 1 };
let _rgW = 0, _rgH = 0;
let _rgMmParams = null;

function rgEdgeTheme(type) {
    if (type.includes('BelongsToMany') || type.includes('MorphToMany'))
        return { stroke:'#c084fc', marker:'url(#rg-arr-mm)',      markerA:'url(#rg-arr-mm-a)',      dash:'7,3' };
    if (type.includes('BelongsTo') || type.includes('MorphTo'))
        return { stroke:'#34d399', marker:'url(#rg-arr-belongs)', markerA:'url(#rg-arr-belongs-a)', dash:'none' };
    if (type.includes('Many'))
        return { stroke:'#818cf8', marker:'url(#rg-arr-many)',    markerA:'url(#rg-arr-many-a)',    dash:'none' };
    return     { stroke:'#2dd4bf', marker:'url(#rg-arr-one)',     markerA:'url(#rg-arr-one-a)',     dash:'5,3' };
}

function initRelGraph() {
    if (graphRendered) return;
    graphRendered = true;

    const models = APP.models || [];
    const svg    = document.getElementById('rg-canvas');
    if (!svg) return;
    _rgW = svg.clientWidth  || 900;
    _rgH = svg.clientHeight || 600;
    const W = _rgW, H = _rgH;
    const edgesG = document.getElementById('rg-edges-g');
    const nodesG = document.getElementById('rg-nodes-g');

    if (!models.length) {
        const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        t.setAttribute('x', W/2); t.setAttribute('y', H/2);
        t.setAttribute('text-anchor', 'middle'); t.setAttribute('fill', '#6B778C');
        t.setAttribute('font-size', '14'); t.setAttribute('font-family', 'system-ui');
        t.textContent = 'No models found';
        nodesG.appendChild(t);
        return;
    }

    // Build node data from APP.models
    const nById = {};
    const nodes = models.map((m, i) => {
        const angle = (i / Math.max(models.length, 1)) * 2 * Math.PI - Math.PI / 2;
        const r     = Math.min(W, H) * 0.32;
        const node  = {
            id:    m.name,
            table: m.table || m.name.toLowerCase() + 's',
            rels:  (m.relationships || []).length,
            x: W/2 + r * Math.cos(angle),
            y: H/2 + r * Math.sin(angle),
            vx: 0, vy: 0,
        };
        nById[m.name] = node;
        return node;
    });

    // Build deduplicated edge list
    const edgeSet = new Map();
    models.forEach(m => {
        (m.relationships || []).forEach(rel => {
            const toName = rel.related ? rel.related.split('\\').pop() : null;
            if (!toName || !nById[toName] || toName === m.name) return;
            const key = m.name + '|' + toName + '|' + rel.type;
            if (!edgeSet.has(key)) edgeSet.set(key, { from: m.name, to: toName, type: rel.type });
        });
    });
    const edges = [...edgeSet.values()];

    // Force simulation (same constants as report.blade.php)
    const REPEL = 7000, IDEAL = 200, SPRING = 0.06, GRAV = 0.003, DAMP = 0.78;
    for (let it = 0; it < 350; it++) {
        for (let a = 0; a < nodes.length; a++) {
            for (let b = a + 1; b < nodes.length; b++) {
                const na = nodes[a], nb = nodes[b];
                const dx = na.x - nb.x, dy = na.y - nb.y;
                const d2 = Math.max(dx*dx + dy*dy, 100), d = Math.sqrt(d2), f = REPEL / d2;
                na.vx += dx/d*f; na.vy += dy/d*f;
                nb.vx -= dx/d*f; nb.vy -= dy/d*f;
            }
        }
        edges.forEach(e => {
            const na = nById[e.from], nb = nById[e.to];
            if (!na || !nb) return;
            const dx = nb.x - na.x, dy = nb.y - na.y;
            const d  = Math.sqrt(dx*dx + dy*dy) || 1, f = (d - IDEAL) * SPRING;
            na.vx += dx/d*f; na.vy += dy/d*f;
            nb.vx -= dx/d*f; nb.vy -= dy/d*f;
        });
        nodes.forEach(n => {
            n.vx += (W/2 - n.x) * GRAV; n.vy += (H/2 - n.y) * GRAV;
            n.vx *= DAMP; n.vy *= DAMP;
            n.x = Math.max(RG_NW/2 + 20, Math.min(W - RG_NW/2 - 20, n.x + n.vx));
            n.y = Math.max(RG_NH/2 + 20, Math.min(H - RG_NH/2 - 20, n.y + n.vy));
        });
    }
    _rgNodes = nodes;

    // Draw edges via createElementNS (same as report.blade.php approach)
    edges.forEach(e => {
        const na = nById[e.from], nb = nById[e.to];
        if (!na || !nb) return;
        const th   = rgEdgeTheme(e.type);
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('class', 'rg-edge-path g-edge');
        path.setAttribute('data-from', e.from);
        path.setAttribute('data-to',   e.to);
        path.setAttribute('data-type', e.type);
        path.setAttribute('fill',           'none');
        path.setAttribute('stroke',         th.stroke);
        path.setAttribute('stroke-width',   '1.5');
        path.setAttribute('stroke-opacity', '0.4');
        path.setAttribute('marker-end',     th.marker);
        if (th.dash !== 'none') path.setAttribute('stroke-dasharray', th.dash);
        _rgSetEdgePath(path, na, nb);
        edgesG.appendChild(path);
    });

    // Draw nodes via createElementNS
    nodes.forEach(n => {
        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('class',     'rg-node-g g-node');
        g.setAttribute('data-id',   n.id);
        g.style.cursor = 'pointer';
        g.setAttribute('transform', 'translate(' + (n.x - RG_NW/2) + ',' + (n.y - RG_NH/2) + ')');

        const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bg.setAttribute('class',        'rg-node-bg g-node-bg');
        bg.setAttribute('width',        RG_NW);
        bg.setAttribute('height',       RG_NH);
        bg.setAttribute('rx',           '10');
        bg.setAttribute('fill',         '#FFFFFF');
        bg.setAttribute('stroke',       '#DFE1E6');
        bg.setAttribute('stroke-width', '1.5');
        bg.setAttribute('filter',       'url(#rg-f-node)');

        const bar = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bar.setAttribute('class',  'rg-node-bar g-node-bar');
        bar.setAttribute('width',  RG_NW);
        bar.setAttribute('height', '5');
        bar.setAttribute('rx',     '5');
        bar.setAttribute('fill',   '#0052CC');

        const nm = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        nm.setAttribute('x',           RG_NW/2);
        nm.setAttribute('y',           '26');
        nm.setAttribute('text-anchor', 'middle');
        nm.setAttribute('font-family', 'ui-monospace,monospace');
        nm.setAttribute('font-size',   '13');
        nm.setAttribute('font-weight', '800');
        nm.setAttribute('fill',        '#172B4D');
        nm.textContent = n.id.length > 17 ? n.id.slice(0, 16) + '…' : n.id;

        const tb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        tb.setAttribute('x',           RG_NW/2);
        tb.setAttribute('y',           '40');
        tb.setAttribute('text-anchor', 'middle');
        tb.setAttribute('font-family', 'ui-monospace,monospace');
        tb.setAttribute('font-size',   '10');
        tb.setAttribute('fill',        '#6B778C');
        tb.textContent = n.table.length > 20 ? n.table.slice(0, 19) + '…' : n.table;

        g.appendChild(bg); g.appendChild(bar); g.appendChild(nm); g.appendChild(tb);

        if (n.rels > 0) {
            const rb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            rb.setAttribute('x',           RG_NW - 8);
            rb.setAttribute('y',           '56');
            rb.setAttribute('text-anchor', 'end');
            rb.setAttribute('font-size',   '9');
            rb.setAttribute('font-weight', '700');
            rb.setAttribute('fill',        '#a5b4fc');
            rb.textContent = n.rels + 'r';
            g.appendChild(rb);
        }
        g.addEventListener('click', ev => { ev.stopPropagation(); rgSelect(n.id); });
        nodesG.appendChild(g);
    });

    // Build adjacency map
    _rgAdj = {};
    nodes.forEach(n => { _rgAdj[n.id] = new Set(); });
    edges.forEach(e => {
        if (nById[e.from] && nById[e.to]) {
            _rgAdj[e.from].add(e.to);
            _rgAdj[e.to].add(e.from);
        }
    });

    // Pan & zoom interaction
    let isPan = false, panOrigin = { x: 0, y: 0 };
    const vpEl = document.getElementById('rg-vp');

    function applyVp() {
        vpEl.setAttribute('transform',
            'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
        _rgUpdateMinimap();
    }

    svg.addEventListener('mousedown', e => {
        if (!e.target.closest('.g-node')) {
            isPan = true; panOrigin = { x: e.clientX, y: e.clientY }; svg.style.cursor = 'grabbing';
        }
    });
    window.addEventListener('mousemove', e => {
        if (!isPan) return;
        _rgVp.x -= (e.clientX - panOrigin.x) / _rgVp.z;
        _rgVp.y -= (e.clientY - panOrigin.y) / _rgVp.z;
        panOrigin = { x: e.clientX, y: e.clientY };
        applyVp();
    });
    window.addEventListener('mouseup', () => { isPan = false; svg.style.cursor = 'grab'; });
    svg.addEventListener('wheel', e => {
        e.preventDefault();
        const rect   = svg.getBoundingClientRect();
        const mouseX = e.clientX - rect.left, mouseY = e.clientY - rect.top;
        const dataX  = _rgVp.x + mouseX / _rgVp.z, dataY = _rgVp.y + mouseY / _rgVp.z;
        _rgVp.z = Math.max(0.25, Math.min(4, _rgVp.z * (e.deltaY > 0 ? 0.88 : 1.14)));
        _rgVp.x = dataX - mouseX / _rgVp.z;
        _rgVp.y = dataY - mouseY / _rgVp.z;
        applyVp();
    }, { passive: false });

    svg.addEventListener('click', e => { if (e.target === svg) rgDiagClear(); });

    _rgInitMinimap(nodes);
    applyVp();
    graphFit();
}

function _rgSetEdgePath(path, na, nb) {
    const dx = nb.x - na.x, dy = nb.y - na.y;
    const d  = Math.sqrt(dx*dx + dy*dy) || 1, nx = dx/d, ny = dy/d;
    const x1 = na.x + nx*(RG_NW/2), y1 = na.y + ny*(RG_NH/2);
    const x2 = nb.x - nx*(RG_NW/2 + 6), y2 = nb.y - ny*(RG_NH/2 + 6);
    const mx = (x1+x2)/2 - ny*28, my = (y1+y2)/2 + nx*28;
    path.setAttribute('d',
        'M' + x1.toFixed(1) + ',' + y1.toFixed(1) +
        ' Q' + mx.toFixed(1) + ',' + my.toFixed(1) +
        ' ' + x2.toFixed(1) + ',' + y2.toFixed(1));
}

function _rgInitMinimap(nodes) {
    const mm = document.getElementById('rg-minimap');
    if (!mm) return;
    const W = _rgW, H = _rgH, mmW = 160, mmH = 100;
    const scale = Math.min(mmW / W, mmH / H) * 0.88;
    const offX  = (mmW - W * scale) / 2, offY = (mmH - H * scale) / 2;

    const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    bg.setAttribute('width', mmW); bg.setAttribute('height', mmH); bg.setAttribute('fill', '#F4F5F7');
    mm.appendChild(bg);

    nodes.forEach(n => {
        const dot = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        dot.setAttribute('x',       offX + (n.x - RG_NW/2) * scale);
        dot.setAttribute('y',       offY + (n.y - RG_NH/2) * scale);
        dot.setAttribute('width',   Math.max(4, RG_NW * scale));
        dot.setAttribute('height',  Math.max(3, RG_NH * scale));
        dot.setAttribute('rx',      '2');
        dot.setAttribute('fill',    '#0052CC');
        dot.setAttribute('opacity', '0.45');
        mm.appendChild(dot);
    });

    const vr = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    vr.setAttribute('id',           'rg-mm-vp');
    vr.setAttribute('fill',         'rgba(0,82,204,0.08)');
    vr.setAttribute('stroke',       '#0052CC');
    vr.setAttribute('stroke-width', '1.5');
    vr.setAttribute('rx',           '2');
    mm.appendChild(vr);

    _rgMmParams = { scale, offX, offY, mmW, mmH };
}

function _rgUpdateMinimap() {
    const vr = document.getElementById('rg-mm-vp');
    if (!vr || !_rgMmParams) return;
    const { scale, offX, offY, mmW, mmH } = _rgMmParams;
    const W = _rgW, H = _rgH;
    const vpW = W / _rgVp.z, vpH = H / _rgVp.z;
    vr.setAttribute('x',      Math.max(0, offX + _rgVp.x * scale));
    vr.setAttribute('y',      Math.max(0, offY + _rgVp.y * scale));
    vr.setAttribute('width',  Math.min(mmW, vpW * scale));
    vr.setAttribute('height', Math.min(mmH, vpH * scale));
}

function rgSelect(id) {
    if (_rgSel === id) { rgDiagClear(); return; }
    _rgSel = id;
    const conn = _rgAdj[id] || new Set();

    document.querySelectorAll('.g-node').forEach(g => {
        const nid = g.getAttribute('data-id');
        const bg  = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (!bg || !bar) return;
        if (nid === id) {
            bg.setAttribute('stroke',       '#0052CC');
            bg.setAttribute('stroke-width', '2.5');
            bg.setAttribute('filter',       'url(#rg-f-node-sel)');
            bar.setAttribute('fill', '#0052CC');
            g.setAttribute('opacity', '1');
        } else if (conn.has(nid)) {
            bg.setAttribute('stroke',       '#34D399');
            bg.setAttribute('stroke-width', '2');
            bg.setAttribute('filter',       'url(#rg-f-node-rel)');
            bar.setAttribute('fill', '#34D399');
            g.setAttribute('opacity', '1');
        } else {
            bg.setAttribute('stroke',       'rgba(148,178,222,0.15)');
            bg.setAttribute('stroke-width', '1.5');
            bg.setAttribute('filter',       'url(#rg-f-node)');
            bar.setAttribute('fill', '#0052CC');
            g.setAttribute('opacity', '0.2');
        }
    });

    document.querySelectorAll('.g-edge').forEach(p => {
        const from = p.getAttribute('data-from'), to = p.getAttribute('data-to');
        const type = p.getAttribute('data-type');
        if (from === id || to === id) {
            const th = rgEdgeTheme(type);
            p.setAttribute('stroke-width',   '2.5');
            p.setAttribute('stroke-opacity', '0.95');
            p.setAttribute('marker-end',     th.markerA);
        } else {
            p.setAttribute('stroke-width',   '1');
            p.setAttribute('stroke-opacity', '0.07');
        }
    });

    // Update info strip
    const n     = _rgNodes.find(n => n.id === id);
    const model = (APP.models || []).find(m => m.name === id);
    const rels  = model ? (model.relationships || []) : [];
    document.getElementById('rg-info-name').textContent  = id;
    document.getElementById('rg-info-table').textContent = n ? n.table : '';
    document.getElementById('rg-info-count').textContent =
        rels.length + ' relationship' + (rels.length !== 1 ? 's' : '');

    // Populate relationship cards
    const cardsEl = document.getElementById('rg-rels-cards');
    cardsEl.innerHTML = '';
    document.getElementById('rg-rels-title').textContent = id + ' relationships';
    rels.forEach(e => {
        const other = e.related ? e.related.split('\\').pop() : '?';
        const th    = rgEdgeTheme(e.type);
        const card  = document.createElement('div');
        card.style.cssText = 'display:flex;flex-direction:column;gap:4px;padding:10px 12px;border-radius:10px;border:1px solid #DFE1E6;border-left:3px solid ' + th.stroke + ';background:#FFFFFF;cursor:pointer;box-shadow:0 1px 4px rgba(23,43,77,0.06);transition:box-shadow 0.2s;';
        card.innerHTML =
            '<div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">' +
                '<span style="font-size:11px;font-weight:700;color:#172B4D;font-family:ui-monospace,monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + other + '</span>' +
                '<span style="font-size:10px;font-family:ui-monospace,monospace;padding:2px 6px;border-radius:4px;background:' + th.stroke + '22;color:' + th.stroke + '">→</span>' +
            '</div>' +
            '<span style="font-size:10px;font-weight:600;color:' + th.stroke + ';font-family:ui-monospace,monospace;">' + e.type + '</span>' +
            '<span style="font-size:10px;color:#6B778C;font-family:ui-monospace,monospace;">' + (e.method || '') + '()</span>';
        cardsEl.appendChild(card);
    });

    document.getElementById('rg-rels-panel').style.display = 'none';
    document.getElementById('rg-rels-chevron').style.transform = '';
    document.getElementById('rg-info-row').style.display = 'flex';
    document.getElementById('rg-legend').style.display = 'none';
    document.getElementById('rg-clear-btn').style.display = '';
}

function rgDiagClear() {
    _rgSel = null;
    const si = document.getElementById('rg-search-input');
    if (si) si.value = '';
    document.querySelectorAll('.g-node').forEach(g => {
        g.setAttribute('opacity', '1');
        const bg  = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (bg)  { bg.setAttribute('stroke', 'rgba(148,178,222,0.2)'); bg.setAttribute('stroke-width', '1.5'); bg.setAttribute('filter', 'url(#rg-f-node)'); }
        if (bar) bar.setAttribute('fill', '#0052CC');
    });
    document.querySelectorAll('.g-edge').forEach(p => {
        const th = rgEdgeTheme(p.getAttribute('data-type'));
        p.setAttribute('stroke',         th.stroke);
        p.setAttribute('stroke-width',   '1.5');
        p.setAttribute('stroke-opacity', '0.4');
        p.setAttribute('marker-end',     th.marker);
    });
    document.getElementById('rg-info-row').style.display = 'none';
    document.getElementById('rg-rels-panel').style.display = 'none';
    document.getElementById('rg-rels-chevron').style.transform = '';
    document.getElementById('rg-legend').style.display = 'flex';
    document.getElementById('rg-clear-btn').style.display = 'none';
}

function rgToggleRels() {
    const panel   = document.getElementById('rg-rels-panel');
    const chevron = document.getElementById('rg-rels-chevron');
    const isHidden = panel.style.display === 'none';
    panel.style.display = isHidden ? 'block' : 'none';
    chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
}

function graphZoom(factor) {
    if (!_rgW) return;
    const W = _rgW, H = _rgH;
    const cx = _rgVp.x + W / (2 * _rgVp.z);
    const cy = _rgVp.y + H / (2 * _rgVp.z);
    _rgVp.z  = Math.max(0.25, Math.min(4, _rgVp.z * factor));
    _rgVp.x  = cx - W / (2 * _rgVp.z);
    _rgVp.y  = cy - H / (2 * _rgVp.z);
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform',
        'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
    _rgUpdateMinimap();
}

function graphFit() {
    if (!_rgNodes.length || !_rgW) return;
    const W = _rgW, H = _rgH;
    const xs = _rgNodes.map(n => n.x), ys = _rgNodes.map(n => n.y);
    const minX = Math.min(...xs) - RG_NW/2 - 20, maxX = Math.max(...xs) + RG_NW/2 + 20;
    const minY = Math.min(...ys) - RG_NH/2 - 20, maxY = Math.max(...ys) + RG_NH/2 + 20;
    _rgVp.z = Math.max(0.25, Math.min(4, Math.min(W / (maxX - minX), H / (maxY - minY))));
    _rgVp.x = minX - (W/_rgVp.z - (maxX - minX)) / 2;
    _rgVp.y = minY - (H/_rgVp.z - (maxY - minY)) / 2;
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform',
        'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
    _rgUpdateMinimap();
}

function graphReset() {
    _rgVp = { x: 0, y: 0, z: 1 };
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform', 'translate(0,0) scale(1)');
    _rgUpdateMinimap();
    rgDiagClear();
}

function graphSearch(query) {
    query = (query || '').toLowerCase().trim();
    if (!query) { rgDiagClear(); return; }
    document.querySelectorAll('.g-node').forEach(g => {
        const nid   = g.getAttribute('data-id');
        const match = nid.toLowerCase().includes(query);
        g.setAttribute('opacity', match ? '1' : '0.12');
        const bg = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (bg) {
            bg.setAttribute('stroke',       match ? '#0052CC' : 'rgba(148,178,222,0.15)');
            bg.setAttribute('stroke-width', match ? '2.5'     : '1.5');
            bg.setAttribute('filter',       match ? 'url(#rg-f-node-sel)' : 'url(#rg-f-node)');
        }
        if (bar) bar.setAttribute('fill', match ? '#0052CC' : 'rgba(148,178,222,0.3)');
    });
    document.querySelectorAll('.g-edge').forEach(p => {
        const from  = p.getAttribute('data-from'), to = p.getAttribute('data-to');
        const match = from.toLowerCase().includes(query) || to.toLowerCase().includes(query);
        p.setAttribute('stroke-opacity', match ? '0.7'  : '0.04');
        p.setAttribute('stroke-width',   match ? '2'    : '1');
    });
    document.getElementById('rg-clear-btn').style.display = '';
}


// ── API Docs ──────────────────────────────────────────────────────────────────

let _apiActiveMethod = 'ALL';

function apiToggle(uid) {
    const detail  = document.getElementById('detail-'  + uid);
    const chevron = document.getElementById('chevron-' + uid);
    if (!detail) return;
    const wasHidden = detail.style.display === 'none' || !detail.style.display;
    detail.style.display = wasHidden ? 'block' : 'none';
    chevron.style.transform = wasHidden ? 'rotate(180deg)' : '';
    if (wasHidden) apiRenderFlow(uid);
}

function apiRenderFlow(uid) {
    const el = document.getElementById('api-flow-' + uid);
    if (!el || el.dataset.rendered) return;
    el.dataset.rendered = '1';

    const ctrl   = el.dataset.controller ? el.dataset.controller.split('\\').pop() : null;
    const action = el.dataset.action || null;
    const method = el.dataset.method || '';
    const uri    = el.dataset.uri || '';
    const rname  = el.dataset.rname || '';
    let mws = [];
    try { mws = JSON.parse(el.dataset.mws || '[]'); } catch(e) { mws = []; }
    const chain = ctrl ? traceChain(ctrl) : [];

    // No chain and no ctrl: nothing to show
    if (!ctrl && !chain.length) return;

    // Build local graph (use unique prefix to avoid DOM id collisions)
    let cnt = 0;
    const pfx    = 'af' + uid.replace(/[^a-z0-9]/gi, '');
    const lNodes = {}, lEdges = [];
    const mkN = (type, name, sub) => {
        const id = pfx + (cnt++);
        lNodes[id] = { id, type, name: name || '', sub: sub || '' };
        return id;
    };
    const mkE = (f, t, label) => lEdges.push({ from: f, to: t, label: label || '' });

    const rId = mkN('request', (method ? method + ' ' : '') + '/' + uri, rname);
    let prev = rId;

    if (mws.length) {
        const mId = mkN('middleware', 'Middleware Stack', mws.length + ' layer' + (mws.length > 1 ? 's' : ''));
        mkE(prev, mId, 'enters');
        prev = mId;
    }

    if (chain.length) {
        let last = prev;
        chain.forEach((node, i) => {
            const sub = i === 0
                ? (action && action !== '__invoke' ? '@' + action : 'Invokable')
                : (RF_TYPE_LABEL[node.layer] || node.layer);
            const nid = mkN(node.layer, node.name, sub);
            mkE(last, nid, i === 0 ? 'dispatches' : (EDGE_LABEL[node.edgeType] || node.edgeType || 'calls'));
            last = nid;
        });
        const mn = chain.find(n => n.layer === 'model');
        if (mn) {
            const md = (APP.models || []).find(m => m.name === mn.name);
            if (md?.table) {
                const dbId  = mkN('database', md.table, 'DB Table');
                const mnId  = Object.values(lNodes).find(n => n.name === mn.name && n.type === 'model')?.id;
                if (mnId) mkE(mnId, dbId, 'queries');
            }
        }
    } else if (ctrl) {
        const cid = mkN('controller', ctrl, action && action !== '__invoke' ? '@' + action : 'Invokable');
        mkE(prev, cid, 'dispatches');
    }

    // Horizontal layout (left → right, single row)
    const NW = 196, NH = 66, GAP_X = 72, PAD = 20;
    const nodeList = Object.values(lNodes);
    const CW = nodeList.length * NW + (nodeList.length - 1) * GAP_X + PAD * 2;
    const CH = NH + PAD * 2;
    const pos = {};
    nodeList.forEach((n, i) => {
        const x = PAD + i * (NW + GAP_X);
        pos[n.id] = { x, y: PAD, cx: x + NW / 2, cy: PAD + NH / 2 };
    });

    // Unique SVG ids
    const dotId = pfx + 'dot', arrId = pfx + 'arr';

    const defs = `<defs>
        <pattern id="${dotId}" x="0" y="0" width="22" height="22" patternUnits="userSpaceOnUse">
            <circle cx="1" cy="1" r="0.8" fill="rgba(148,178,222,0.15)" opacity="1"/>
        </pattern>
        <marker id="${arrId}" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="rgba(148,178,222,0.5)"/>
        </marker>
    </defs>`;

    const edgesSvg = lEdges.map(e => {
        const f = pos[e.from], t = pos[e.to];
        if (!f || !t) return '';
        const x1 = f.x + NW, y1 = f.cy, x2 = t.x, y2 = t.cy;
        const cp = (x2 - x1) * 0.45;
        const d  = `M${x1},${y1} C${x1+cp},${y1} ${x2-cp},${y2} ${x2},${y2}`;
        const mx = (x1 + x2) / 2, my = (y1 + y2) / 2;
        const lw = (e.label.length * 6) + 14;
        return `<g>
            <path d="${d}" fill="none" stroke="rgba(148,178,222,0.35)" stroke-width="1.5" marker-end="url(#${arrId})"/>
            ${e.label ? `<rect x="${mx-lw/2}" y="${my-8}" width="${lw}" height="16" rx="8" fill="#FFFFFF" stroke="#DFE1E6" stroke-width="1"/>
            <text x="${mx}" y="${my+4}" fill="#6B778C" font-size="9" font-family="ui-monospace,monospace" text-anchor="middle" font-weight="600">${_esc(e.label)}</text>` : ''}
        </g>`;
    }).join('');

    const nodesSvg = nodeList.map(n => {
        const p = pos[n.id]; if (!p) return '';
        const c  = RF_COLOR[n.type] || RF_COLOR.unknown;
        const tl = RF_TYPE_LABEL[n.type] || n.type;
        const sn = n.name.length > 21 ? n.name.slice(0, 20) + '…' : n.name;
        const ss = n.sub.length  > 24 ? n.sub.slice(0, 23)  + '…' : n.sub;
        return `<g>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="12" fill="${c.bg}" stroke="${c.border}" stroke-width="1.5"/>
            <rect x="${p.x+1.5}" y="${p.y+1.5}" width="${NW-3}" height="18" rx="10" fill="${c.border}" fill-opacity="0.15"/>
            <circle cx="${p.x+12}" cy="${p.y+10}" r="3" fill="${c.dot}"/>
            <text x="${p.x+21}" y="${p.y+14}" fill="${c.type}" font-size="7.5" font-family="ui-monospace,monospace" font-weight="700" letter-spacing="0.1em">${tl.toUpperCase()}</text>
            <text x="${p.x+10}" y="${p.y+40}" fill="${c.name}" font-size="11.5" font-family="ui-monospace,monospace" font-weight="700">${_esc(sn)}</text>
            ${ss ? `<text x="${p.x+10}" y="${p.y+56}" fill="${c.sub}" font-size="9.5" font-family="ui-monospace,monospace">${_esc(ss)}</text>` : ''}
        </g>`;
    }).join('');

    el.innerHTML = `
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px;padding-top:4px">
            <div style="width:3px;height:14px;border-radius:2px;background:#0052CC;flex-shrink:0"></div>
            <p style="font-size:10px;font-weight:700;color:#6B778C;text-transform:uppercase;letter-spacing:0.1em;margin:0;font-family:ui-monospace,monospace">Request Flow</p>
            <span style="font-size:9px;color:#6B778C;font-family:ui-monospace,monospace">${nodeList.length} nodes · ${lEdges.length} edges</span>
        </div>
        <div style="overflow-x:auto;border-radius:12px;border:1px solid #DFE1E6;background:#F7F8F9">
            <svg width="${CW}" height="${CH}" style="display:block;min-width:${CW}px">
                ${defs}
                <rect width="${CW}" height="${CH}" fill="#F7F8F9"/>
                <rect width="${CW}" height="${CH}" fill="url(#${dotId})"/>
                ${edgesSvg}
                ${nodesSvg}
            </svg>
        </div>`;
}

function apiScrollTo(groupName) {
    const el = document.getElementById('api-group-' + groupName);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.api-nav-item').forEach(a => {
            a.style.background = 'var(--bg-elevated)';
            a.style.color      = 'var(--text-dim)';
            a.style.borderColor= 'var(--border)';
        });
        const tab = document.querySelector(`.api-nav-item[data-group-tab="${groupName}"]`);
        if (tab) {
            tab.style.background  = 'rgba(0,82,204,0.12)';
            tab.style.color       = 'var(--cyan)';
            tab.style.borderColor = 'rgba(0,82,204,0.35)';
        }
    }
}

function apiFilter(method) {
    _apiActiveMethod = method;
    document.querySelectorAll('.api-filter-btn').forEach(btn => {
        const active = btn.dataset.method === method;
        btn.style.background    = active ? 'var(--bg-hover)' : 'transparent';
        btn.style.color         = active ? 'var(--cyan)'     : 'var(--text-dim)';
        btn.style.borderColor   = active ? 'var(--cyan)'     : 'var(--border)';
        btn.style.fontWeight    = active ? '700'             : '500';
    });
    _apiApplyFilters();
}

function apiSearch(q) {
    _apiApplyFilters(q.toLowerCase().trim());
}

function _apiApplyFilters(q) {
    const query  = q ?? (document.getElementById('api-search')?.value || '').toLowerCase().trim();
    const method = _apiActiveMethod;

    document.querySelectorAll('.api-endpoint-wrap').forEach(ep => {
        const matchMethod = method === 'ALL' || ep.dataset.method === method;
        const matchQuery  = !query || ep.dataset.uri.includes(query);
        ep.style.display  = matchMethod && matchQuery ? '' : 'none';
    });

    // Hide group headers if all their endpoints are hidden
    document.querySelectorAll('.api-group').forEach(grp => {
        const visible = [...grp.querySelectorAll('.api-endpoint-wrap')].some(ep => ep.style.display !== 'none');
        grp.style.display = visible ? '' : 'none';
    });
}

// ── Boot ──────────────────────────────────────────────────────────────────────
mermaid.initialize({
    startOnLoad: false,
    theme: 'base',
    themeVariables: {
        background: '#FFFFFF',
        primaryColor: '#EAF2FF',
        primaryBorderColor: '#0052CC',
        primaryTextColor: '#172B4D',
        lineColor: '#6B778C',
        secondaryColor: '#F4F5F7',
        tertiaryColor: '#F3F0FF',
        edgeLabelBackground: '#FFFFFF',
        fontFamily: "'Inter', sans-serif",
    },
    flowchart: { rankSpacing:80, nodeSpacing:40, curve:'basis', padding:20 }
});

navigate('overview');

// ── Architecture Explorer ────────────────────────────────────────────────────
const OV_VIOLET = [101,84,192], OV_CYAN_RGB = [0,82,204];
function _lerpColor(a, b, t) {
    return `rgb(${Math.round(a[0]+(b[0]-a[0])*t)},${Math.round(a[1]+(b[1]-a[1])*t)},${Math.round(a[2]+(b[2]-a[2])*t)})`;
}
function _svgEl(tag, attrs) {
    const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    Object.entries(attrs || {}).forEach(([k, v]) => el.setAttribute(k, v));
    return el;
}

let _ovArchScale = 1;

function _buildOvArchDiagram() {
    const host = document.getElementById('ovArchDiagram');
    if (!host) return;

    const MAX_NODES = 10; // max controllers or models shown as individual nodes
    const BOX_W = 200, BOX_H = 52;

    const allCtrls  = APP.controllers || [];
    const allModels = APP.models || [];
    const routes    = APP.routes || [];
    const depEdges  = (APP.dependencies?.edges || []);

    // ── Controllers: deduplicate, sort by method count desc, cap at MAX_NODES ──
    const seenCtrl = new Set();
    const sortedCtrls = [...allCtrls]
        .sort((a, b) => (b.method_count || 0) - (a.method_count || 0))
        .filter(c => { if (seenCtrl.has(c.name)) return false; seenCtrl.add(c.name); return true; });

    const visibleCtrls = sortedCtrls.slice(0, MAX_NODES);
    const extraCtrlCnt = sortedCtrls.length - visibleCtrls.length;

    const ctrlNodes = visibleCtrls.map((c, i) => ({
        id: 'c-' + i,
        label: c.name.replace(/Controller$/, ''),   // strip suffix for readability
        sub: (c.method_count || 0) + ' methods',
        rawName: c.name,
        isMore: false,
    }));
    if (extraCtrlCnt > 0) {
        ctrlNodes.push({ id:'c-more', label:`+ ${extraCtrlCnt} more`, sub:'controllers', rawName:null, isMore:true });
    }

    // ── Build ctrl→model edges from real dependency data ──
    const ctrlNameToId = {};
    ctrlNodes.forEach(n => { if (n.rawName) ctrlNameToId[n.rawName] = n.id; });

    const realCtrlModelEdges = []; // [ctrlId, modelName]
    const referencedModelNames = new Set();
    depEdges.forEach(e => {
        const cId = ctrlNameToId[e.from];
        if (cId && e.to) {
            realCtrlModelEdges.push([cId, e.to]);
            referencedModelNames.add(e.to);
        }
    });

    // ── Models: prioritise those referenced by dep edges, cap at MAX_NODES ──
    const allModelsSorted = [
        ...allModels.filter(m => referencedModelNames.has(m.name)),
        ...allModels.filter(m => !referencedModelNames.has(m.name)),
    ].slice(0, MAX_NODES);

    const extraModelCnt = allModels.length - allModelsSorted.length;

    const modelNodes = allModelsSorted.map((m, i) => ({
        id: 'm-' + i, label: m.name, sub: m.table || 'model', rawName: m.name, isMore: false,
    }));
    if (extraModelCnt > 0) {
        modelNodes.push({ id:'m-more', label:`+ ${extraModelCnt} more`, sub:'models', rawName:null, isMore:true });
    }
    const modelNameToId = {};
    modelNodes.forEach(n => { if (n.rawName) modelNameToId[n.rawName] = n.id; });

    // ── Route file nodes ──
    const webCnt   = routes.filter(r => (r.middleware||[]).includes('web')).length;
    const apiCnt   = routes.filter(r => (r.middleware||[]).includes('api')).length;
    const otherCnt = routes.length - webCnt - apiCnt;
    const routeNodes = [];
    if (webCnt > 0)   routeNodes.push({ id:'r-web',   label:'web.php',   sub: webCnt   + ' routes' });
    if (apiCnt > 0)   routeNodes.push({ id:'r-api',   label:'api.php',   sub: apiCnt   + ' routes' });
    if (otherCnt > 0) routeNodes.push({ id:'r-other', label:'routes',    sub: otherCnt + ' routes' });
    if (!routeNodes.length) routeNodes.push({ id:'r-all', label:'Routes', sub: routes.length + ' total' });

    const LAYERS = [
        { name:'Application', nodes: [{ id:'app', label:(APP.project?.name)||'Laravel App', sub:'HTTP Kernel' }] },
        { name:'Routes',      nodes: routeNodes },
        { name:'Controllers', nodes: ctrlNodes.length ? ctrlNodes : [{ id:'c-all', label:'Controllers', sub: allCtrls.length + ' total', isMore:true }] },
        { name:'Models',      nodes: modelNodes.length ? modelNodes : [{ id:'m-all', label:'Models', sub: allModels.length + ' total', isMore:true }] },
    ];

    // ── Edge list ──
    const EDGES = [];

    // App → each route file
    LAYERS[1].nodes.forEach(r => EDGES.push(['app', r.id]));

    // Routes → top 3 evenly-spread controllers (representative flow, not one-per-ctrl)
    const realCtrlNodes = LAYERS[2].nodes.filter(n => !n.isMore);
    if (realCtrlNodes.length) {
        LAYERS[1].nodes.forEach(r => {
            const picks = realCtrlNodes.length <= 3
                ? realCtrlNodes
                : [realCtrlNodes[0], realCtrlNodes[Math.floor(realCtrlNodes.length / 2)], realCtrlNodes[realCtrlNodes.length - 1]];
            picks.forEach(c => EDGES.push([r.id, c.id]));
        });
    }

    // Controllers → Models: real dependency edges first
    realCtrlModelEdges.forEach(([cId, mName]) => {
        const mId = modelNameToId[mName];
        if (mId) EDGES.push([cId, mId]);
    });

    // Fallback: if no real edges, connect each visible ctrl to the model at same relative position
    if (realCtrlModelEdges.length === 0 && realCtrlNodes.length && modelNodes.length) {
        const realModelNodes = LAYERS[3].nodes.filter(n => !n.isMore);
        realCtrlNodes.forEach((c, i) => {
            const m = realModelNodes[i % realModelNodes.length];
            if (m) EDGES.push([c.id, m.id]);
        });
    }

    // ── Layout ──
    const n = LAYERS.length;
    const maxNodes = Math.max(...LAYERS.map(l => l.nodes.length));
    const NODE_SPACING = 64;
    const BAND_TOP = 60;
    const BAND_BTM = Math.max(420, BAND_TOP + (maxNodes - 1) * NODE_SPACING);
    const VB_W = 1240, VB_H = BAND_BTM + 70;
    const colX = LAYERS.map((_, i) => 100 + i * ((VB_W - 220) / (n - 1)));
    const positions = {};

    LAYERS.forEach((layer, li) => {
        const cnt = layer.nodes.length;
        layer.nodes.forEach((node, ni) => {
            const y = cnt === 1
                ? (BAND_TOP + BAND_BTM) / 2
                : BAND_TOP + ni * ((BAND_BTM - BAND_TOP) / (cnt - 1));
            positions[node.id] = { x: colX[li], y, layer: li, label: node.label, sub: node.sub, layerName: layer.name, isMore: !!node.isMore };
        });
    });

    // ── SVG ──
    const svg = _svgEl('svg', { viewBox:`0 0 ${VB_W} ${VB_H}`, width:VB_W, height:VB_H, style:'display:block;overflow:visible;' });

    // Drop-shadow filter for node boxes
    const ovDefs = _svgEl('defs');
    const ovFilter = _svgEl('filter', { id:'ov-shadow', x:'-20%', y:'-30%', width:'140%', height:'160%' });
    const ovFds = _svgEl('feDropShadow', { dx:'0', dy:'2', stdDeviation:'4' });
    ovFds.setAttribute('flood-color', 'rgba(23,43,77,0.10)');
    ovFds.setAttribute('flood-opacity', '1');
    ovFilter.appendChild(ovFds);
    ovDefs.appendChild(ovFilter);
    svg.appendChild(ovDefs);

    // Layer header labels
    LAYERS.forEach((layer, li) => {
        const t = li / (n - 1);
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const tx = _svgEl('text', { x:colX[li], y:22, 'text-anchor':'middle', 'font-family':'Inter,sans-serif', 'font-size':10, 'font-weight':700, 'letter-spacing':'0.08em', fill:color });
        tx.textContent = layer.name.toUpperCase();
        svg.appendChild(tx);
        // Subtle column separator line
        if (li > 0) {
            const sep = _svgEl('line', { x1: colX[li] - (colX[1]-colX[0])/2, y1: 30, x2: colX[li] - (colX[1]-colX[0])/2, y2: VB_H - 20, stroke:'rgba(23,43,77,0.08)', 'stroke-width':'1' });
            svg.appendChild(sep);
        }
    });

    // Edges with traveling dots
    const edgeGroup = _svgEl('g');
    const edgeEls = [];
    EDGES.forEach(([from, to], i) => {
        const a = positions[from], b = positions[to]; if (!a || !b) return;
        const x1 = a.x + BOX_W/2, y1 = a.y, x2 = b.x - BOX_W/2, y2 = b.y, midX = (x1+x2)/2;
        const d = `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`;
        const t = (a.layer + b.layer) / (2*(n-1));
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const path = _svgEl('path', { d, id:`ov-edge-${i}`, fill:'none', stroke:color, 'stroke-width':'1.5' });
        path.dataset.from = from; path.dataset.to = to;
        path.style.opacity = '0.3';
        edgeGroup.appendChild(path);
        edgeEls.push(path);
        const dot = _svgEl('circle', { r:'2.5', fill:color });
        const anim = _svgEl('animateMotion', { dur:'3s', repeatCount:'indefinite', begin:`${(i*0.3).toFixed(2)}s` });
        const mp = _svgEl('mpath'); mp.setAttributeNS('http://www.w3.org/1999/xlink','href',`#ov-edge-${i}`);
        anim.appendChild(mp); dot.appendChild(anim);
        const opAnim = _svgEl('animate', { attributeName:'opacity', values:'0;1;1;0', keyTimes:'0;0.08;0.9;1', dur:'3s', repeatCount:'indefinite', begin:`${(i*0.3).toFixed(2)}s` });
        dot.appendChild(opAnim); edgeGroup.appendChild(dot);
    });
    svg.appendChild(edgeGroup);

    // Node boxes
    const nodeGroup = _svgEl('g');
    const nodeEls = [];
    Object.entries(positions).forEach(([id, pos]) => {
        const t = pos.layer / (n-1);
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const g = _svgEl('g', { class:'ov-arch-node', tabindex:'0', role:'button', 'aria-label':pos.label });
        g.dataset.id = id;
        const x = pos.x - BOX_W/2, y = pos.y - BOX_H/2;

        // "more" nodes get a dashed, dimmer style
        const rectFill   = pos.isMore ? 'rgba(0,82,204,0.05)' : '#FFFFFF';
        const rectStroke = pos.isMore ? `rgba(0,82,204,0.30)` : color;
        const rectDash   = pos.isMore ? '4,3' : 'none';
        const rect = _svgEl('rect', { x, y, width:BOX_W, height:BOX_H, rx:'10', fill:rectFill, stroke:rectStroke, 'stroke-width':'1.5', 'stroke-dasharray':rectDash, filter:'url(#ov-shadow)' });

        const dotR = pos.isMore ? '2.5' : '4';
        const dotEl = _svgEl('circle', { cx:x+16, cy:y+BOX_H/2, r:dotR, fill: pos.isMore ? 'rgba(0,82,204,0.4)' : color });

        // Label: max 22 chars (wider box allows more)
        const lbl = _svgEl('text', { x:x+30, y:y+20, 'font-family':'Inter,sans-serif', 'font-size':'11', 'font-weight': pos.isMore ? '500' : '600', fill: pos.isMore ? '#6B778C' : '#172B4D' });
        lbl.textContent = pos.label.length > 22 ? pos.label.slice(0, 22) + '…' : pos.label;

        const sub = _svgEl('text', { x:x+30, y:y+36, 'font-family':'JetBrains Mono,monospace', 'font-size':'9.5', fill:'#6B778C' });
        sub.textContent = pos.sub || '';

        g.appendChild(rect); g.appendChild(dotEl); g.appendChild(lbl); g.appendChild(sub);
        nodeGroup.appendChild(g); nodeEls.push(g);
    });
    svg.appendChild(nodeGroup);
    host.innerHTML = ''; host.appendChild(svg);

    // Hover highlight / detail
    const detail = document.getElementById('ovArchDetail');
    const defaultDetail = detail ? detail.innerHTML : '';
    const neighbors = {};
    EDGES.forEach(([f, t]) => { (neighbors[f]=neighbors[f]||new Set()).add(t); (neighbors[t]=neighbors[t]||new Set()).add(f); });

    function ovHighlight(id) {
        const rel = neighbors[id] || new Set();
        edgeEls.forEach(p => { const c = p.dataset.from===id||p.dataset.to===id; p.style.opacity=c?'0.9':'0.05'; p.style.strokeWidth=c?'2.2':'1.5'; });
        nodeEls.forEach(nd => { nd.style.opacity=(nd.dataset.id===id||rel.has(nd.dataset.id))?'1':'0.25'; });
        if (detail) { const pos=positions[id]; detail.textContent = `${pos.label} · ${pos.sub||pos.layerName} — ${rel.size} connection${rel.size===1?'':'s'}`; }
    }
    function ovReset() {
        edgeEls.forEach(p => { p.style.opacity='0.3'; p.style.strokeWidth='1.5'; });
        nodeEls.forEach(nd => { nd.style.opacity='1'; });
        if (detail) detail.innerHTML = defaultDetail;
    }
    nodeEls.forEach(nd => {
        nd.addEventListener('mouseenter', () => ovHighlight(nd.dataset.id));
        nd.addEventListener('focus',      () => ovHighlight(nd.dataset.id));
        nd.addEventListener('mouseleave', ovReset);
        nd.addEventListener('blur',       ovReset);
    });

    // Zoom controls
    const zoomIn  = document.getElementById('ovZoomIn');
    const zoomOut = document.getElementById('ovZoomOut');
    function _applyOvZoom() { svg.setAttribute('width', VB_W*_ovArchScale); svg.setAttribute('height', VB_H*_ovArchScale); }
    if (zoomIn)  zoomIn.addEventListener('click',  () => { _ovArchScale = Math.min(1.7, _ovArchScale+0.15); _applyOvZoom(); });
    if (zoomOut) zoomOut.addEventListener('click', () => { _ovArchScale = Math.max(0.6, _ovArchScale-0.15); _applyOvZoom(); });
}

// Reveal animation for Architecture Explorer panel
(function() {
    if (!window.IntersectionObserver) {
        document.querySelectorAll('[data-ov-reveal]').forEach(el => el.classList.add('ov-in'));
        return;
    }
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('ov-in'); io.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('[data-ov-reveal]').forEach(el => io.observe(el));
})();

_buildOvArchDiagram();

// ── Dependency Graph (custom layered SVG) ────────────────────────────────────

const _DEP_NW  = 114;   // node width
const _DEP_NH  = 32;    // node height
const _DEP_HG  = 10;    // horizontal gap between nodes
const _DEP_MR  = 9;     // max nodes per row within a layer
const _DEP_RG  = 14;    // gap between rows within a layer
const _DEP_LG  = 80;    // gap between layers

const _DEP_CFG = {
    // ATLAS dark theme: dark bg, bright stroke, label is the border/text color
    controller: { label:'Controllers', color:'#0052CC', bg:'#EAF2FF', order:0 },
    job:        { label:'Jobs',        color:'#FF5630', bg:'#FFF4E5', order:1 },
    event:      { label:'Events',      color:'#BF40BF', bg:'#FFF0FB', order:1 },
    listener:   { label:'Listeners',   color:'#DA62AC', bg:'#FEE4FA', order:2 },
    service:    { label:'Services',    color:'#00875A', bg:'#E3FCEF', order:2 },
    repository: { label:'Repositories',color:'#FF8B00', bg:'#FFFAE6', order:3 },
    model:      { label:'Models',      color:'#6554C0', bg:'#F3F0FF', order:4 },
    database:   { label:'Database',    color:'#6B778C', bg:'#F4F5F7', order:5 },
};

let _depT  = { tx:0, ty:0, s:1 };
let _depDrag = null;
let _depPos  = {};
let _depSel  = null;
const NS = 'http://www.w3.org/2000/svg';

function initDepGraph() {
    const nodes = (APP.dependencies || {}).nodes || [];
    const edges = (APP.dependencies || {}).edges || [];
    if (!nodes.length) return;

    const canvas  = document.getElementById('dep-canvas');
    const bandsG  = document.getElementById('dep-bands-g');
    const edgesG  = document.getElementById('dep-edges-g');
    const nodesG  = document.getElementById('dep-nodes-g');
    if (!canvas) return;

    // Group nodes by layer order
    const byOrder = {};
    nodes.forEach(n => {
        const cfg = _DEP_CFG[n.layer] || { order: 4 };
        (byOrder[cfg.order] = byOrder[cfg.order] || []).push(n);
    });

    // Build positions — layered layout
    let curY = 30;
    const layerBands = []; // { y1, y2, order }

    Object.keys(byOrder).sort((a,b)=>+a-+b).forEach(order => {
        const layerNodes = byOrder[order];
        // Split into rows of _DEP_MR
        const rows = [];
        for (let i = 0; i < layerNodes.length; i += _DEP_MR) {
            rows.push(layerNodes.slice(i, i + _DEP_MR));
        }
        const maxCols = Math.max(...rows.map(r => r.length));
        const bandY1 = curY;

        rows.forEach((row, ri) => {
            const rowW   = row.length * (_DEP_NW + _DEP_HG) - _DEP_HG;
            const maxW   = maxCols * (_DEP_NW + _DEP_HG) - _DEP_HG;
            const startX = -maxW / 2 + (maxW - rowW) / 2;   // center this row

            row.forEach((n, ci) => {
                _depPos[n.name] = {
                    x: startX + ci * (_DEP_NW + _DEP_HG),
                    y: curY,
                    layer: n.layer,
                };
            });
            curY += _DEP_NH + (ri < rows.length - 1 ? _DEP_RG : 0);
        });

        layerBands.push({ y1: bandY1, y2: curY, order: +order });
        curY += _DEP_LG;
    });

    // Draw faint layer bands (background stripes)
    const allX = Object.values(_depPos).map(p => p.x);
    const bandMinX = Math.min(...allX) - 20;
    const bandMaxX = Math.max(...allX) + _DEP_NW + 20;

    layerBands.forEach(band => {
        // Find one node in this order to get layer name
        const repNode = byOrder[band.order]?.[0];
        if (!repNode) return;
        const cfg = _DEP_CFG[repNode.layer] || {};
        const rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x', bandMinX);
        rect.setAttribute('y', band.y1 - 8);
        rect.setAttribute('width', bandMaxX - bandMinX);
        rect.setAttribute('height', band.y2 - band.y1 + 16);
        rect.setAttribute('rx', '10');
        rect.setAttribute('fill', cfg.color || '#6B778C');
        rect.setAttribute('opacity', '0.08');
        bandsG.appendChild(rect);

        // Layer label on left
        const lbl = document.createElementNS(NS, 'text');
        lbl.setAttribute('x', bandMinX + 6);
        lbl.setAttribute('y', band.y1 + (band.y2 - band.y1) / 2 + 4);
        lbl.setAttribute('font-size', '10');
        lbl.setAttribute('font-family', 'system-ui,sans-serif');
        lbl.setAttribute('fill', cfg.color || '#64748b');
        lbl.setAttribute('font-weight', '600');
        lbl.setAttribute('opacity', '0.7');
        lbl.textContent = cfg.label || '';
        bandsG.appendChild(lbl);
    });

    // Draw edges (bezier curves)
    edges.forEach(e => {
        const fp = _depPos[e.from];
        const tp = _depPos[e.to];
        if (!fp || !tp) return;

        const x1 = fp.x + _DEP_NW / 2;
        const y1 = fp.y + _DEP_NH;
        const x2 = tp.x + _DEP_NW / 2;
        const y2 = tp.y;
        const cy = (y1 + y2) / 2;

        const path = document.createElementNS(NS, 'path');
        path.setAttribute('d', `M${x1},${y1} C${x1},${cy} ${x2},${cy} ${x2},${y2}`);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', 'rgba(148,178,222,0.4)');
        path.setAttribute('stroke-width', '1.5');
        path.setAttribute('marker-end', 'url(#dep-arr)');
        path.setAttribute('opacity', '1');
        path.dataset.from = e.from;
        path.dataset.to   = e.to;
        edgesG.appendChild(path);
    });

    // Draw nodes
    nodes.forEach(n => {
        const pos = _depPos[n.name];
        if (!pos) return;
        const cfg = _DEP_CFG[n.layer] || { color:'#6B778C', bg:'#F4F5F7' };

        const g = document.createElementNS(NS, 'g');
        g.style.cursor = 'pointer';
        g.dataset.name = n.name;

        const rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x', pos.x);
        rect.setAttribute('y', pos.y);
        rect.setAttribute('width', _DEP_NW);
        rect.setAttribute('height', _DEP_NH);
        rect.setAttribute('rx', '7');
        rect.setAttribute('fill', '#FFFFFF');
        rect.setAttribute('stroke', cfg.color);
        rect.setAttribute('stroke-width', '1.5');
        rect.setAttribute('filter', 'url(#dep-shadow)');

        // Truncate display name: strip suffix, add ellipsis
        const suffixes = /Controller$|Service$|Repository$|Observer$|Policy$|Listener$/;
        const short = n.name.replace(suffixes, '');
        const display = short.length > 13 ? short.substring(0, 12) + '…' : short;

        const text = document.createElementNS(NS, 'text');
        text.setAttribute('x', pos.x + _DEP_NW / 2);
        text.setAttribute('y', pos.y + _DEP_NH / 2 + 4);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size', '10.5');
        text.setAttribute('font-family', 'system-ui,sans-serif');
        text.setAttribute('font-weight', '600');
        text.setAttribute('fill', '#172B4D');
        text.textContent = display;

        const title = document.createElementNS(NS, 'title');
        title.textContent = n.name;

        g.appendChild(rect); g.appendChild(text); g.appendChild(title);

        g.addEventListener('click',       () => depNodeClick(n.name));
        g.addEventListener('mouseenter',  () => depHighlight(n.name));
        g.addEventListener('mouseleave',  () => { if (_depSel !== n.name) depClearHighlight(false); });

        nodesG.appendChild(g);
    });

    // Fit on first render
    depFit();

    // Zoom (scroll wheel)
    canvas.addEventListener('wheel', e => {
        e.preventDefault();
        const rect   = canvas.getBoundingClientRect();
        const mx     = e.clientX - rect.left;
        const my     = e.clientY - rect.top;
        const delta  = e.deltaY > 0 ? -0.1 : 0.1;
        const newS   = Math.max(0.12, Math.min(3, _depT.s + delta));
        _depT.tx    += (mx - _depT.tx) * (1 - newS / _depT.s);
        _depT.ty    += (my - _depT.ty) * (1 - newS / _depT.s);
        _depT.s      = newS;
        _depApplyT();
    }, { passive: false });

    // Pan (drag)
    canvas.addEventListener('mousedown', e => {
        if (e.target.closest('g[data-name]')) return;
        _depDrag = { sx: e.clientX - _depT.tx, sy: e.clientY - _depT.ty };
        canvas.style.cursor = 'grabbing';
    });
    window.addEventListener('mousemove', e => {
        if (!_depDrag) return;
        _depT.tx = e.clientX - _depDrag.sx;
        _depT.ty = e.clientY - _depDrag.sy;
        _depApplyT();
    });
    window.addEventListener('mouseup', () => {
        _depDrag = null;
        if (canvas) canvas.style.cursor = 'grab';
    });
}

function _depApplyT() {
    const vp = document.getElementById('dep-vp');
    if (vp) vp.setAttribute('transform', `translate(${_depT.tx},${_depT.ty}) scale(${_depT.s})`);
}

function depFit() {
    const canvas = document.getElementById('dep-canvas');
    if (!canvas || !Object.keys(_depPos).length) return;

    const allX = Object.values(_depPos).map(p => p.x);
    const allY = Object.values(_depPos).map(p => p.y);
    const minX = Math.min(...allX), maxX = Math.max(...allX) + _DEP_NW;
    const minY = Math.min(...allY), maxY = Math.max(...allY) + _DEP_NH;
    const gW = maxX - minX, gH = maxY - minY;
    const cW = canvas.clientWidth, cH = canvas.clientHeight;
    const pad = 48;

    _depT.s  = Math.min((cW - pad*2) / gW, (cH - pad*2) / gH, 1.4);
    _depT.tx = cW/2 - _depT.s * (minX + gW/2);
    _depT.ty = cH/2 - _depT.s * (minY + gH/2);
    _depApplyT();
}

function depZoom(delta) {
    const canvas = document.getElementById('dep-canvas');
    const cW = canvas?.clientWidth || 800;
    const cH = canvas?.clientHeight || 600;
    const newS = Math.max(0.12, Math.min(3, _depT.s + delta));
    _depT.tx += (cW/2 - _depT.tx) * (1 - newS / _depT.s);
    _depT.ty += (cH/2 - _depT.ty) * (1 - newS / _depT.s);
    _depT.s   = newS;
    _depApplyT();
}

function depNodeClick(name) {
    if (_depSel === name) {
        _depSel = null;
        depClearHighlight();
        const lbl = document.getElementById('dep-sel-label');
        if (lbl) lbl.style.display = 'none';
    } else {
        _depSel = name;
        depHighlight(name);
        const lbl = document.getElementById('dep-sel-label');
        if (lbl) { lbl.textContent = name; lbl.style.display = 'block'; }
    }
}

function depHighlight(name) {
    const edges = (APP.dependencies || {}).edges || [];
    const connected = new Set([name]);
    edges.forEach(e => {
        if (e.from === name) connected.add(e.to);
        if (e.to   === name) connected.add(e.from);
    });

    document.querySelectorAll('#dep-edges-g path').forEach(p => {
        const on = p.dataset.from === name || p.dataset.to === name;
        p.setAttribute('stroke',       on ? '#0052CC' : 'rgba(148,178,222,0.15)');
        p.setAttribute('stroke-width', on ? '2'       : '1.5');
        p.setAttribute('opacity',      on ? '1'       : '0.5');
        p.setAttribute('marker-end',   on ? 'url(#dep-arr-hi)' : 'url(#dep-arr)');
    });

    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = connected.has(g.dataset.name) ? '1' : '0.18';
    });
}

function depClearHighlight(resetSel = true) {
    if (resetSel) _depSel = null;
    document.querySelectorAll('#dep-edges-g path').forEach(p => {
        p.setAttribute('stroke',       'rgba(148,178,222,0.4)');
        p.setAttribute('stroke-width', '1.5');
        p.setAttribute('opacity',      '1');
        p.setAttribute('marker-end',   'url(#dep-arr)');
    });
    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = '1';
    });
    const lbl = document.getElementById('dep-sel-label');
    if (lbl && resetSel) lbl.style.display = 'none';
}

// ── AI Chat ───────────────────────────────────────────────────────────────────

const CHAT_ENDPOINT = '{{ route("architecture.ai.chat") }}';
let _chatBusy = false;

function chatSuggest(text) {
    document.getElementById('chat-input').value = text;
    chatPreviewContext(text);
    chatSend();
}

function chatSend() {
    const input = document.getElementById('chat-input');
    const msg   = input.value.trim();
    if (!msg || _chatBusy) return;

    _chatBusy = true;
    input.value = '';
    document.getElementById('chat-context-hint').textContent = '';
    document.getElementById('chat-empty').style.display = 'none';

    // Extract context
    const {data: ctx, labels} = chatExtractContext(msg);

    // Append user bubble
    chatAppendBubble('user', msg);

    // Append loading AI bubble
    const loadingId = 'chat-loading-' + Date.now();
    chatAppendBubble('ai', null, loadingId, labels);

    fetch(CHAT_ENDPOINT, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
        body:    JSON.stringify({ message: msg, context: ctx }),
    })
    .then(r => r.json())
    .then(json => {
        if (json.error) throw new Error(json.error);
        chatReplaceBubble(loadingId, json.reply, labels);
    })
    .catch(err => {
        chatReplaceBubble(loadingId, '**Error:** ' + err.message, labels, true);
    })
    .finally(() => { _chatBusy = false; });
}

function chatPreviewContext(msg) {
    const {labels} = chatExtractContext(msg);
    const hint = document.getElementById('chat-context-hint');
    hint.textContent = labels.length ? 'Context: ' + labels.join(' · ') : '';
}

function chatExtractContext(question) {
    const q      = question.toLowerCase();
    const words  = (q.match(/\b\w{4,}\b/g) || []);
    const labels = [];
    const data   = {};

    data.project = APP.project;
    data.summary = APP.summary;

    // Fat/large controller queries
    if (/large|fat|big|most method|too many|which.*controller|longest/.test(q)) {
        const sorted = [...(APP.controllers || [])].sort((a, b) => (b.method_count || 0) - (a.method_count || 0));
        data.controllers_by_size = sorted.slice(0, 10);
        labels.push('Controllers sorted by size');
    }

    // Controller keyword match
    const ctrlHits = (APP.controllers || []).filter(c => words.some(w => c.name.toLowerCase().includes(w)));
    if (ctrlHits.length) { data.controllers = ctrlHits; labels.push(ctrlHits.map(c => c.name).join(', ')); }

    // Model keyword match
    const modelHits = (APP.models || []).filter(m => words.some(w => m.name.toLowerCase().includes(w)));
    if (modelHits.length) { data.models = modelHits; labels.push(modelHits.map(m => m.name).join(', ')); }

    // Route keyword match
    const routeHits = (APP.routes || []).filter(r =>
        words.some(w => (r.uri || '').toLowerCase().includes(w) || (r.controller?.class || '').toLowerCase().includes(w))
    );
    if (routeHits.length) { data.routes = routeHits.slice(0, 20); labels.push(routeHits.length + ' routes'); }

    // Service keyword match
    const svcHits = (APP.services || []).filter(s => words.some(w => (s.name || '').toLowerCase().includes(w)));
    if (svcHits.length) { data.services = svcHits; labels.push(svcHits.map(s => s.name).join(', ')); }

    // Score / SOLID / quality
    if (/score|solid|grade|quality|best practice|principle/.test(q)) {
        data.score = APP.score; labels.push('Architecture Score');
    }

    // Dependencies / coupling
    if (/depend|inject|coupl|graph|layer/.test(q)) {
        data.dependencies = APP.dependencies; labels.push('Dependency Graph');
    }

    // Modules
    if (/module/.test(q) && (APP.modules || []).length) {
        data.modules = APP.modules; labels.push('Modules');
    }

    // Jobs / Events
    if (/job|queue|dispatch/.test(q) && (APP.jobs || []).length) {
        data.jobs = APP.jobs; labels.push('Jobs');
    }
    if (/event|listener|broadcast/.test(q) && (APP.events || []).length) {
        data.events = APP.events; labels.push('Events');
    }

    // Fallback — send a compact summary
    if (labels.length === 0) {
        data.controllers = (APP.controllers || []).slice(0, 15);
        data.models      = (APP.models || []).slice(0, 15);
        data.score       = APP.score;
        labels.push('Full architecture summary');
    }

    return { data, labels };
}

function chatAppendBubble(role, text, loadingId = null, contextLabels = []) {
    const wrap = document.getElementById('chat-messages');
    const isAI = role === 'ai';
    const id   = loadingId || ('msg-' + Date.now());

    const ctxHtml = contextLabels.length && isAI
        ? `<p class="text-xs text-slate-400 mt-2 pt-2 border-t border-slate-100">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    const bodyHtml = text === null
        ? `<span class="inline-flex gap-1 items-center text-slate-400 text-sm"><span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:0s"></span><span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:.15s"></span><span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:.3s"></span></span>`
        : `<div class="prose-sm text-sm text-slate-700 leading-relaxed">${chatMarkdown(text)}</div>`;

    wrap.insertAdjacentHTML('beforeend', `
        <div class="flex ${isAI ? 'justify-start' : 'justify-end'} gap-2.5" id="${id}">
            ${isAI ? `<div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0 mt-0.5">AI</div>` : ''}
            <div class="max-w-[80%] rounded-2xl px-4 py-3 ${isAI ? 'bg-white border border-slate-200 rounded-tl-sm' : 'bg-indigo-600 text-white rounded-tr-sm'}">
                ${bodyHtml}
                ${ctxHtml}
            </div>
            ${!isAI ? `<div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-xs font-bold shrink-0 mt-0.5">You</div>` : ''}
        </div>
    `);

    wrap.scrollTop = wrap.scrollHeight;
    return id;
}

function chatReplaceBubble(id, text, contextLabels = [], isError = false) {
    const el = document.getElementById(id);
    if (!el) return;
    const inner = el.querySelector('div.max-w-\\[80\\%\\]');
    if (!inner) return;

    const ctxHtml = contextLabels.length
        ? `<p class="text-xs text-slate-400 mt-2 pt-2 border-t border-slate-100">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    inner.innerHTML = `<div class="prose-sm text-sm ${isError ? 'text-red-600' : 'text-slate-700'} leading-relaxed">${chatMarkdown(text)}</div>${ctxHtml}`;

    const wrap = document.getElementById('chat-messages');
    wrap.scrollTop = wrap.scrollHeight;
}

function chatMarkdown(text) {
    return text
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/```(\w*)\n?([\s\S]*?)```/g, (_, lang, code) =>
            `<pre class="bg-slate-800 text-green-300 rounded-lg p-3 mt-2 mb-2 text-xs overflow-x-auto"><code>${code.trim()}</code></pre>`)
        .replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-indigo-700 px-1 rounded text-xs">$1</code>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/^### (.+)$/gm, '<p class="font-semibold text-slate-800 mt-3 mb-1">$1</p>')
        .replace(/^## (.+)$/gm,  '<p class="font-bold text-slate-800 mt-3 mb-1 text-base">$1</p>')
        .replace(/^# (.+)$/gm,   '<p class="font-bold text-slate-900 mt-3 mb-2 text-lg">$1</p>')
        .replace(/^- (.+)$/gm,   '<li class="ml-4 list-disc">$1</li>')
        .replace(/\n\n/g, '<br>')
        .replace(/\n/g, ' ');
}

// ── AI Docs ────────────────────────────────────────────────────────────────────

const DOCS_ENDPOINT = '{{ route("architecture.ai.documentation") }}';
const _docsContent  = {};
const DOC_TYPES     = ['architecture','models','controllers','routes','services','modules'];

async function docsGenerate(type) {
    const btn    = document.getElementById('doc-gen-btn-' + type);
    const status = document.getElementById('doc-status-' + type);
    const dlBtn  = document.getElementById('doc-dl-btn-' + type);

    btn.disabled = true;
    btn.innerHTML = `<svg style="width:12px;height:12px;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Generating…`;
    status.textContent = 'Generating…';
    status.style.color = 'var(--cyan)';

    try {
        const res  = await fetch(DOCS_ENDPOINT, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
            body:    JSON.stringify({ type }),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Server error');

        _docsContent[type] = { content: json.content, filename: json.filename };

        status.textContent = '✔ Ready';
        status.style.color = 'var(--emerald)';
        btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Re-generate`;
        btn.disabled = false;
        dlBtn.style.display = 'inline-flex';

        // Show "Download All" if at least one doc is ready
        document.getElementById('docs-download-all-btn').style.display = 'inline-flex';

    } catch (err) {
        status.textContent = '✘ Failed';
        status.style.color = 'var(--rose)';
        btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Retry`;
        btn.disabled = false;
    }
}

async function docsGenerateAll() {
    for (const type of DOC_TYPES) {
        await docsGenerate(type);
    }
}

function docsDownload(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    _downloadBlob(doc.content, doc.filename, 'text/markdown');
}

function docsDownloadAll() {
    const ready = DOC_TYPES.filter(t => _docsContent[t]);
    ready.forEach((type, i) => {
        setTimeout(() => docsDownload(type), i * 300);
    });
}

// ── AI Graphic Report ─────────────────────────────────────────────────────────

async function generateAIGraphicReport() {
    const btn    = document.getElementById('ai-report-btn');
    const label  = document.getElementById('ai-report-btn-label');
    const panel  = document.getElementById('ai-report-progress');
    const errEl  = document.getElementById('ai-report-error');
    const spinner = document.getElementById('ai-report-spinner');
    const title  = document.getElementById('ai-report-progress-title');

    btn.disabled  = true;
    label.textContent = 'Generating…';
    panel.style.display = 'block';
    errEl.style.display = 'none';
    errEl.textContent = '';

    // Reset all step icons
    document.querySelectorAll('#ai-report-steps .step-icon').forEach(el => {
        el.style.cssText = 'width:14px;height:14px;border-radius:50%;border:2px solid rgba(148,178,222,0.4);flex-shrink:0;display:inline-block;background:none;';
        el.innerHTML = '';
    });

    const _stepDone  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;background:#34D399;border:none;'; el.innerHTML = '<svg viewBox="0 0 16 16" fill="none" width="10" height="10"><path stroke="white" stroke-width="2" stroke-linecap="round" fill="none" d="M3 8l3.5 3.5 6-7"/></svg>'; }
    };
    const _stepActive = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;border:2px solid #A78BFA;background:rgba(167,139,250,0.2);flex-shrink:0;display:inline-block;animation:pulse 1s infinite;'; }
    };
    const _stepFail  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;flex-shrink:0;display:inline-block;background:#FBBF24;border:none;'; }
    };

    let aiAnalysis = null;
    const aiDocs   = {};

    try {
        // ── Step 1: AI analyze ──────────────────────────────────────────────
        _stepActive('analyze');
        const analyzeRes  = await fetch(AI_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
        });
        const analyzeJson = await analyzeRes.json();
        if (!analyzeRes.ok || analyzeJson.error) throw new Error(analyzeJson.error || 'AI analysis failed');
        aiAnalysis = analyzeJson;
        _stepDone('analyze');

        // ── Steps 2–7: AI docs per section ─────────────────────────────────
        for (const type of DOC_TYPES) {
            _stepActive(type);
            try {
                const res  = await fetch(DOCS_ENDPOINT, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
                    body:    JSON.stringify({ type }),
                });
                const json = await res.json();
                if (!res.ok || json.error) throw new Error(json.error);
                aiDocs[type] = json.content;
                _stepDone(type);
            } catch(e) {
                // Non-fatal: skip section, mark with warning
                _stepFail(type);
                aiDocs[type] = null;
            }
        }

        // ── Step 8: Build & download ────────────────────────────────────────
        _stepActive('build');
        const html = _buildAIGraphicReport(APP, aiAnalysis, aiDocs);
        _downloadBlob(html, 'ai-architecture-report.html', 'text/html;charset=utf-8');
        _stepDone('build');

        title.textContent = 'Report ready — downloading!';
        spinner.style.display = 'none';

    } catch(err) {
        errEl.textContent = 'Error: ' + err.message;
        errEl.style.display = 'block';
        title.textContent = 'Generation failed';
        spinner.style.display = 'none';
    } finally {
        btn.disabled = false;
        label.textContent = 'Generate AI Graphic Report';
    }
}

function _mdToHtml(md) {
    if (!md) return '';
    let html = _esc(md);
    // Code blocks (must be before inline code)
    html = html.replace(/```[\w]*\n?([\s\S]*?)```/g, (_, code) =>
        `<pre style="background:#1e293b;color:#e2e8f0;border-radius:10px;padding:16px;overflow-x:auto;font-family:ui-monospace,monospace;font-size:13px;line-height:1.6;margin:12px 0">${code.trim()}</pre>`
    );
    // Inline code
    html = html.replace(/`([^`]+)`/g, '<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-family:ui-monospace,monospace;font-size:0.9em">$1</code>');
    // Headings
    html = html.replace(/^### (.+)$/gm, '<h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:20px 0 8px">$1</h3>');
    html = html.replace(/^## (.+)$/gm,  '<h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:28px 0 10px;padding-bottom:6px;border-bottom:2px solid #e2e8f0">$1</h2>');
    html = html.replace(/^# (.+)$/gm,   '<h1 style="font-size:26px;font-weight:900;color:#0f172a;margin:0 0 16px">$1</h1>');
    // Bold / italic
    html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*([^*]+)\*/g,     '<em>$1</em>');
    // Unordered lists
    html = html.replace(/((?:^- .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n').map(l => `<li style="margin-bottom:4px">${l.replace(/^- /,'')}</li>`).join('');
        return `<ul style="list-style:disc;padding-left:20px;margin:8px 0">${items}</ul>`;
    });
    // Ordered lists
    html = html.replace(/((?:^\d+\. .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n').map(l => `<li style="margin-bottom:4px">${l.replace(/^\d+\. /,'')}</li>`).join('');
        return `<ol style="list-style:decimal;padding-left:20px;margin:8px 0">${items}</ol>`;
    });
    // Horizontal rule
    html = html.replace(/^---+$/gm, '<hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0"/>');
    // Paragraphs
    html = html.split(/\n\n+/).map(block => {
        if (block.match(/^<(h[1-3]|ul|ol|pre|hr)/)) return block;
        const trimmed = block.trim();
        return trimmed ? `<p style="margin:0 0 12px;color:#334155;line-height:1.7">${trimmed}</p>` : '';
    }).join('\n');

    return html;
}

function _buildAIGraphicReport(d, ai, docs) {
    const esc   = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const proj  = d.project?.name ?? 'Laravel App';
    const score = d.score?.score  ?? 0;
    const grade = d.score?.grade  ?? '';
    const s     = d.summary       ?? {};
    const rs    = d.route_summary ?? {};

    // ── Score gauge SVG ─────────────────────────────────────────────────────
    const R = 64, CX = 80, CY = 80, SW = 14;
    const circ   = 2 * Math.PI * R;
    const arc    = circ * 0.75;
    const offset = arc - (arc * score / 100);
    const gColor = score >= 80 ? '#10b981' : score >= 60 ? '#f59e0b' : '#ef4444';
    const gaugeSvg = `<svg width="160" height="160" viewBox="0 0 160 160">
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}" stroke-linecap="round" stroke-dasharray="${arc} ${circ}" stroke-dashoffset="0" transform="rotate(135 ${CX} ${CY})"/>
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="${gColor}" stroke-width="${SW}" stroke-linecap="round" stroke-dasharray="${arc} ${circ}" stroke-dashoffset="${offset}" transform="rotate(135 ${CX} ${CY})"/>
        <text x="${CX}" y="${CY - 8}" text-anchor="middle" font-size="28" font-weight="800" fill="#1e293b" font-family="system-ui,sans-serif">${score}</text>
        <text x="${CX}" y="${CY + 14}" text-anchor="middle" font-size="12" font-weight="600" fill="${gColor}" font-family="system-ui,sans-serif">${esc(grade)}</text>
        <text x="${CX}" y="${CY + 30}" text-anchor="middle" font-size="9" fill="#94a3b8" font-family="system-ui,sans-serif">/ 100</text>
    </svg>`;

    // ── Component stat cards ─────────────────────────────────────────────────
    const stats = [
        ['Models',       s.models??0,        '#8b5cf6','#f5f3ff','#ede9fe'],
        ['Controllers',  s.controllers??0,   '#3b82f6','#eff6ff','#dbeafe'],
        ['Routes',       s.routes??0,        '#10b981','#f0fdf4','#d1fae5'],
        ['Services',     s.services??0,      '#06b6d4','#ecfeff','#cffafe'],
        ['Repositories', s.repositories??0,  '#f59e0b','#fffbeb','#fef3c7'],
        ['Jobs',         s.jobs??0,          '#f97316','#fff7ed','#ffedd5'],
        ['Events',       s.events??0,        '#d946ef','#fdf4ff','#fae8ff'],
        ['Policies',     s.policies??0,      '#64748b','#f8fafc','#f1f5f9'],
        ['API Routes',   rs.api??0,          '#0891b2','#ecfeff','#cffafe'],
        ['Named Routes', rs.named_count??0,  '#7c3aed','#f5f3ff','#ede9fe'],
    ];
    const statCards = stats.map(([name, count, color, bg, border]) =>
        `<div style="background:${bg};border:1px solid ${border};border-radius:14px;padding:16px 18px">
            <div style="font-size:26px;font-weight:800;color:${color};font-family:system-ui,sans-serif">${count}</div>
            <div style="font-size:11px;color:#64748b;font-weight:500;margin-top:2px">${esc(name)}</div>
        </div>`
    ).join('');

    // ── Score checks ─────────────────────────────────────────────────────────
    const checkRows = (d.score?.checks ?? []).map(c => {
        const icon  = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
        const color = c.status === 'pass' ? '#10b981' : c.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<tr style="border-bottom:1px solid #f1f5f9">
            <td style="padding:9px 10px;font-size:15px;color:${color}">${icon}</td>
            <td style="padding:9px 10px;font-size:13px;font-weight:600;color:#1e293b">${esc(c.label)}</td>
            <td style="padding:9px 10px;font-size:12px;color:#64748b">${esc(c.note ?? '')}</td>
        </tr>`;
    }).join('');

    // ── AI problems ──────────────────────────────────────────────────────────
    const sevColor = { error:'#ef4444', warning:'#f59e0b', info:'#3b82f6' };
    const sevBg    = { error:'#fef2f2', warning:'#fffbeb', info:'#eff6ff' };
    const problemCards = (ai?.problems ?? []).map(p => {
        const col = sevColor[p.severity] ?? '#64748b';
        const bg  = sevBg[p.severity]   ?? '#f8fafc';
        return `<div style="background:${bg};border:1px solid ${col}30;border-left:4px solid ${col};border-radius:10px;padding:14px 16px;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <span style="font-size:11px;font-weight:700;color:${col};text-transform:uppercase;letter-spacing:0.06em;background:${col}20;padding:2px 8px;border-radius:20px">${esc(p.severity)}</span>
                <strong style="font-size:13px;color:#1e293b">${esc(p.title)}</strong>
            </div>
            <p style="font-size:13px;color:#475569;margin:0 0 6px;line-height:1.6">${esc(p.description)}</p>
            ${p.location ? `<code style="font-size:11px;color:#64748b;background:#f1f5f9;padding:2px 8px;border-radius:6px">${esc(p.location)}</code>` : ''}
        </div>`;
    }).join('') || '<p style="color:#94a3b8;font-size:13px">No problems detected.</p>';

    // ── AI suggestions ───────────────────────────────────────────────────────
    const priColor = { high:'#ef4444', medium:'#f59e0b', low:'#10b981' };
    const suggCards = (ai?.suggestions ?? []).map(p => {
        const col = priColor[p.priority] ?? '#64748b';
        return `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <span style="font-size:11px;font-weight:700;color:${col};background:${col}20;padding:2px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em">${esc(p.priority)}</span>
                <strong style="font-size:13px;color:#1e293b">${esc(p.title)}</strong>
            </div>
            <p style="font-size:13px;color:#475569;margin:0 0 6px;line-height:1.6">${esc(p.description)}</p>
            ${p.example ? `<pre style="font-size:12px;color:#1e293b;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;margin:6px 0 0;overflow-x:auto;font-family:ui-monospace,monospace">${esc(p.example)}</pre>` : ''}
        </div>`;
    }).join('') || '<p style="color:#94a3b8;font-size:13px">No suggestions available.</p>';

    // ── SOLID review ─────────────────────────────────────────────────────────
    const solidCards = Object.entries(ai?.solid_review ?? {}).map(([letter, data]) => {
        const col = data.status === 'pass' ? '#10b981' : data.status === 'warn' ? '#f59e0b' : '#ef4444';
        const bg  = data.status === 'pass' ? '#f0fdf4' : data.status === 'warn' ? '#fffbeb' : '#fef2f2';
        const fullName = { S:'Single Responsibility', O:'Open / Closed', L:'Liskov Substitution', I:'Interface Segregation', D:'Dependency Inversion' };
        return `<div style="background:${bg};border:1px solid ${col}30;border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="width:36px;height:36px;border-radius:50%;background:${col};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff;flex-shrink:0">${esc(letter)}</div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:${col};text-transform:uppercase;margin:0">${data.status}</p>
                    <p style="font-size:12px;font-weight:600;color:#1e293b;margin:0">${esc(fullName[letter] ?? letter)}</p>
                </div>
            </div>
            <p style="font-size:12px;color:#475569;margin:0;line-height:1.6">${esc(data.note ?? '')}</p>
        </div>`;
    }).join('');

    // ── Best practices ───────────────────────────────────────────────────────
    const bpItems = (ai?.laravel_best_practices ?? []).map(bp => {
        const icon  = bp.status === 'pass' ? '✔' : bp.status === 'warn' ? '⚠' : '✘';
        const color = bp.status === 'pass' ? '#10b981' : bp.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f1f5f9">
            <span style="font-size:14px;color:${color};flex-shrink:0;width:18px">${icon}</span>
            <div>
                <p style="font-size:13px;font-weight:600;color:#1e293b;margin:0 0 2px">${esc(bp.name)}</p>
                <p style="font-size:12px;color:#64748b;margin:0">${esc(bp.note)}</p>
            </div>
        </div>`;
    }).join('');

    // ── Dependency graph ─────────────────────────────────────────────────────
    const depSvg = _buildDepSvg(d.dependencies?.nodes ?? [], d.dependencies?.edges ?? []);

    // ── Section helper ───────────────────────────────────────────────────────
    const sec = (title, color, content) =>
        `<section style="margin-bottom:52px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px">
                <div style="width:4px;height:34px;border-radius:2px;background:${color};flex-shrink:0"></div>
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;font-family:system-ui,sans-serif">${esc(title)}</h2>
            </div>
            ${content}
        </section>`;

    const docSection = (type, label, color) => {
        if (!docs[type]) return '';
        return sec(label, color,
            `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:32px">${_mdToHtml(docs[type])}</div>`
        );
    };

    // ── Full HTML ─────────────────────────────────────────────────────────────
    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>AI Architecture Report — ${esc(proj)}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f8fafc;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;line-height:1.5}
@media print{body{background:#fff}.no-print{display:none!important}}
</style>
</head>
<body>

<!-- HEADER -->
<div style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 60%,#4c1d95 100%);padding:48px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap">
    <div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:20px;padding:4px 14px;margin-bottom:16px">
            <span style="font-size:11px;color:#a5b4fc;font-weight:700;letter-spacing:0.1em">AI-POWERED ARCHITECTURE REPORT</span>
        </div>
        <h1 style="font-size:36px;font-weight:900;color:#fff;margin-bottom:10px">${esc(proj)}</h1>
        <div style="display:flex;gap:20px;flex-wrap:wrap">
            <span style="font-size:13px;color:#a5b4fc">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">Generated ${esc(d.generated_at ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">Provider: ${esc(ai?.provider ?? d.ai_provider ?? 'AI')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:20px;padding:24px 32px;flex-shrink:0">
        <div style="font-size:52px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:15px;font-weight:700;color:#a78bfa;margin-top:6px">${esc(grade)}</div>
        <div style="font-size:11px;color:#6d6d9a;margin-top:4px">Architecture Score</div>
    </div>
</div>

<!-- AI SUMMARY BANNER -->
${ai?.summary ? `<div style="background:#f0f9ff;border-bottom:2px solid #bfdbfe;padding:24px 48px">
    <p style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px">AI Executive Summary</p>
    <p style="font-size:15px;color:#1e3a5f;line-height:1.7;max-width:900px">${esc(ai.summary)}</p>
</div>` : ''}

<!-- BODY -->
<div style="max-width:1200px;margin:0 auto;padding:48px 32px">

    <!-- Stats Grid -->
    ${sec('Component Overview', '#4f46e5',
        `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px">${statCards}</div>`
    )}

    <!-- Score -->
    ${sec('Architecture Score', '#10b981',
        `<div style="display:grid;grid-template-columns:160px 1fr;gap:24px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;align-items:start">
            ${gaugeSvg}
            <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif"><tbody>${checkRows}</tbody></table>
        </div>`
    )}

    <!-- SOLID Review -->
    ${solidCards ? sec('SOLID Principles', '#6366f1',
        `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px">${solidCards}</div>`
    ) : ''}

    <!-- Best Practices -->
    ${bpItems ? sec('Laravel Best Practices', '#10b981',
        `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:8px 20px">${bpItems}</div>`
    ) : ''}

    <!-- Problems -->
    ${sec('Issues Detected', '#ef4444',
        `<div>${problemCards}</div>`
    )}

    <!-- Suggestions -->
    ${sec('AI Suggestions', '#f59e0b',
        `<div>${suggCards}</div>`
    )}

    <!-- Dependency Graph -->
    ${depSvg ? sec('Dependency Graph', '#6366f1',
        `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden"><div style="overflow-x:auto;padding:20px">${depSvg}</div></div>`
    ) : ''}

    <!-- AI Documentation sections -->
    ${docSection('architecture', 'Architecture Overview', '#4f46e5')}
    ${docSection('models',       'Models Documentation',  '#8b5cf6')}
    ${docSection('controllers',  'Controllers Documentation', '#3b82f6')}
    ${docSection('routes',       'Routes Documentation',  '#10b981')}
    ${docSection('services',     'Services Documentation','#f59e0b')}
    ${docSection('modules',      'Modules Documentation', '#06b6d4')}

</div>

<!-- FOOTER -->
<div style="background:#1e293b;color:#64748b;text-align:center;padding:28px;font-size:12px;font-family:system-ui,sans-serif">
    AI Architecture Report · Generated by <strong style="color:#94a3b8">laravel-architecture-discovery</strong> · ${esc(d.generated_at ?? '')}
</div>

</body>
</html>`;
}

// ── Export helpers ────────────────────────────────────────────────────────────

function exportJson() {
    _downloadBlob(
        JSON.stringify(APP, null, 2),
        'architecture.json',
        'application/json'
    );
}

function copyJson() {
    const btn  = document.getElementById('copy-json-btn');
    const text = JSON.stringify(APP, null, 2);
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => { btn.innerHTML = orig; }, 1800);
    }).catch(() => {
        btn.title = 'Copy failed — try JSON download instead';
    });
}

function exportMarkdown() {
    const d   = APP;
    const s   = d.summary || {};
    const rs  = d.route_summary || {};
    const sc  = d.score || {};
    const out = [];

    out.push('# Architecture Report — ' + (d.project?.name || 'Laravel Application'));
    out.push('');
    out.push('> Generated: ' + d.generated_at);
    out.push('> Laravel ' + d.laravel_version + ' · PHP ' + d.php_version + ' · laravel-architecture-discovery v' + d.package_version);
    out.push('');
    out.push('---');
    out.push('');

    if (sc.score !== undefined) {
        out.push('## Architecture Score');
        out.push('');
        out.push('**' + sc.score + ' / ' + sc.max + '** — ' + sc.grade);
        out.push('');
        (sc.checks || []).forEach(c => {
            const icon = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
            out.push(icon + ' ' + c.label + (c.note ? ' — *' + c.note + '*' : ''));
        });
        out.push('');
        out.push('---');
        out.push('');
    }

    out.push('## Summary');
    out.push('');
    out.push('| Component | Count |');
    out.push('|-----------|------:|');
    const rows = [
        ['Models', s.models], ['Controllers', s.controllers], ['Routes', s.routes],
        ['Jobs', s.jobs], ['Events', s.events], ['Services', s.services],
        ['Repositories', s.repositories], ['Observers', s.observers],
        ['Policies', s.policies], ['Modules', s.modules], ['Packages', s.packages],
        ['Dep. Edges', (d.dependencies?.edges || []).length],
    ];
    rows.forEach(([label, count]) => { if (count) out.push('| ' + label + ' | ' + count + ' |'); });
    out.push('');

    if ((d.dependencies?.edges || []).length > 0) {
        out.push('---');
        out.push('');
        out.push('## Dependency Graph');
        out.push('');
        out.push('```mermaid');
        out.push('flowchart TD');
        const nodes = d.dependencies.nodes || [];
        const edges = d.dependencies.edges || [];
        const byLayer = {};
        nodes.forEach(n => { const l = n.layer || 'model'; (byLayer[l] = byLayer[l] || []).push(n.name); });
        ['controller','job','event','listener','service','repository','model'].forEach(layer => {
            if (!(byLayer[layer] || []).length) return;
            out.push('    subgraph ' + layer.charAt(0).toUpperCase() + layer.slice(1) + 's');
            byLayer[layer].forEach(nm => out.push('        ' + nm));
            out.push('    end');
        });
        edges.forEach(e => out.push('    ' + e.from + ' --> ' + e.to));
        out.push('```');
        out.push('');
    }

    out.push('---');
    out.push('');
    out.push('## Models');
    out.push('');
    (d.models || []).forEach(m => {
        out.push('### ' + m.name);
        out.push('');
        out.push('**Table:** `' + m.table + '`');
        if ((m.fillable || []).length) out.push('**Fillable:** `' + m.fillable.join('`, `') + '`');
        if ((m.relationships || []).length) {
            out.push('');
            out.push('| Method | Type | Related |');
            out.push('|--------|------|---------|');
            m.relationships.forEach(r => out.push('| `' + r.method + '` | `' + r.type + '` | `' + (r.related || '').split('\\').pop() + '` |'));
        }
        out.push('');
    });

    out.push('---');
    out.push('');
    out.push('## Routes');
    out.push('');
    out.push('| Method | URI | Controller | Name |');
    out.push('|--------|-----|------------|------|');
    (d.routes || []).forEach(r => {
        const methods = (r.methods || []).filter(m => m !== 'HEAD').join(',');
        const ctrl    = (r.controller?.class || '').split('\\').pop() || '—';
        const name    = r.name || '—';
        out.push('| ' + methods + ' | `' + r.uri + '` | ' + ctrl + ' | ' + name + ' |');
    });

    _downloadBlob(out.join('\n'), 'architecture.md', 'text/markdown');
}

// ── Graphic Report ────────────────────────────────────────────────────────────

function exportGraphicHTML() {
    const btn   = document.getElementById('graphic-report-btn');
    const label = document.getElementById('graphic-report-label');
    btn.disabled = true;
    label.textContent = 'Building…';

    try {
        const html = _buildGraphicReport(APP);
        const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
        const a    = Object.assign(document.createElement('a'), {
            href:     URL.createObjectURL(blob),
            download: 'architecture-report.html',
        });
        a.click();
        URL.revokeObjectURL(a.href);
    } catch(e) {
        alert('Report generation failed: ' + e.message);
    } finally {
        btn.disabled     = false;
        label.textContent = 'Generate & Download';
    }
}

function _buildGraphicReport(d) {
    const esc   = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const proj  = d.project?.name  ?? 'Laravel App';
    const score = d.score?.score   ?? 0;
    const grade = d.score?.grade   ?? '';
    const s     = d.summary        ?? {};
    const rs    = d.route_summary  ?? {};

    // ── Score gauge SVG ──────────────────────────────────────────────────────
    const R = 64, CX = 80, CY = 80, SW = 14;
    const circ     = 2 * Math.PI * R;
    const arc      = circ * 0.75;           // 270° sweep
    const offset   = arc - (arc * score / 100);
    const gColor   = score >= 80 ? '#10b981' : score >= 60 ? '#f59e0b' : '#ef4444';
    const gaugeSvg = `<svg width="160" height="160" viewBox="0 0 160 160">
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}" stroke-linecap="round"
            stroke-dasharray="${arc} ${circ}" stroke-dashoffset="0" transform="rotate(135 ${CX} ${CY})"/>
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="${gColor}" stroke-width="${SW}" stroke-linecap="round"
            stroke-dasharray="${arc} ${circ}" stroke-dashoffset="${offset}" transform="rotate(135 ${CX} ${CY})"/>
        <text x="${CX}" y="${CY - 8}" text-anchor="middle" font-size="28" font-weight="800" fill="#1e293b" font-family="system-ui,sans-serif">${score}</text>
        <text x="${CX}" y="${CY + 14}" text-anchor="middle" font-size="12" font-weight="600" fill="${gColor}" font-family="system-ui,sans-serif">${esc(grade)}</text>
        <text x="${CX}" y="${CY + 30}" text-anchor="middle" font-size="9" fill="#94a3b8" font-family="system-ui,sans-serif">/ 100</text>
    </svg>`;

    // ── Route method bars ────────────────────────────────────────────────────
    const methodColors = { GET:'#10b981', POST:'#3b82f6', PUT:'#f59e0b', PATCH:'#f97316', DELETE:'#ef4444' };
    const byMethod     = rs.by_method ?? {};
    const maxMCount    = Math.max(1, ...Object.values(byMethod));
    const routeBarsSvg = `<svg width="260" height="${Math.max(40, Object.keys(byMethod).length * 38 + 10)}" font-family="system-ui,sans-serif">
        ${Object.entries(byMethod).map(([m, cnt], i) => {
            const bw  = Math.max(4, Math.round((cnt / maxMCount) * 180));
            const col = methodColors[m] ?? '#64748b';
            const y   = i * 38 + 6;
            return `<rect x="0" y="${y}" width="${bw}" height="22" rx="6" fill="${col}" opacity="0.85"/>
                    <text x="${bw + 8}" y="${y + 15}" font-size="12" font-weight="700" fill="${col}">${cnt}</text>
                    <text x="${bw + 34}" y="${y + 15}" font-size="11" fill="#64748b">${esc(m)}</text>`;
        }).join('')}
    </svg>`;

    // ── Dependency graph SVG ─────────────────────────────────────────────────
    const depNodes = d.dependencies?.nodes ?? [];
    const depEdges = d.dependencies?.edges ?? [];
    const depSvg   = _buildDepSvg(depNodes, depEdges);

    // ── Stat cards HTML ──────────────────────────────────────────────────────
    const stats = [
        ['Models',       s.models       ?? 0, '#8b5cf6', '#f5f3ff', '#ede9fe'],
        ['Controllers',  s.controllers  ?? 0, '#3b82f6', '#eff6ff', '#dbeafe'],
        ['Routes',       s.routes       ?? 0, '#10b981', '#f0fdf4', '#d1fae5'],
        ['Services',     s.services     ?? 0, '#06b6d4', '#ecfeff', '#cffafe'],
        ['Repositories', s.repositories ?? 0, '#f59e0b', '#fffbeb', '#fef3c7'],
        ['Jobs',         s.jobs         ?? 0, '#f97316', '#fff7ed', '#ffedd5'],
        ['Events',       s.events       ?? 0, '#d946ef', '#fdf4ff', '#fae8ff'],
        ['Observers',    s.observers    ?? 0, '#ec4899', '#fdf2f8', '#fce7f3'],
        ['Policies',     s.policies     ?? 0, '#64748b', '#f8fafc', '#f1f5f9'],
        ['Modules',      s.modules      ?? 0, '#4f46e5', '#eef2ff', '#e0e7ff'],
        ['API Routes',   rs.api         ?? 0, '#0891b2', '#ecfeff', '#cffafe'],
        ['Named Routes', rs.named_count ?? 0, '#7c3aed', '#f5f3ff', '#ede9fe'],
    ];
    const statCards = stats.map(([name, count, color, bg, border]) =>
        `<div style="background:${bg};border:1px solid ${border};border-radius:14px;padding:16px 20px;display:flex;flex-direction:column;gap:4px">
            <span style="font-size:24px;font-weight:800;color:${color};font-family:system-ui,sans-serif">${count}</span>
            <span style="font-size:12px;color:#64748b;font-family:system-ui,sans-serif;font-weight:500">${esc(name)}</span>
        </div>`
    ).join('');

    // ── Score checks ─────────────────────────────────────────────────────────
    const checkRows = (d.score?.checks ?? []).map(c => {
        const icon  = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
        const color = c.status === 'pass' ? '#10b981' : c.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<tr>
            <td style="padding:8px 12px;font-size:13px;color:${color};font-weight:700">${icon}</td>
            <td style="padding:8px 12px;font-size:13px;color:#1e293b;font-weight:500">${esc(c.label)}</td>
            <td style="padding:8px 12px;font-size:12px;color:#64748b">${esc(c.note ?? '')}</td>
        </tr>`;
    }).join('');

    // ── Models table ─────────────────────────────────────────────────────────
    const modelRows = (d.models ?? []).slice(0, 40).map(m => {
        const rels = (m.relationships ?? []).map(r => r.type + ':' + (r.related ?? '').split('\\').pop()).join(', ');
        const fill = (m.fillable ?? []).slice(0, 5).join(', ') + ((m.fillable ?? []).length > 5 ? ' …' : '');
        return `<tr>
            <td style="padding:9px 12px;font-weight:700;color:#1e293b;font-family:ui-monospace,monospace;font-size:13px">${esc(m.name)}</td>
            <td style="padding:9px 12px;color:#64748b;font-family:ui-monospace,monospace;font-size:12px">${esc(m.table ?? '')}</td>
            <td style="padding:9px 12px;color:#64748b;font-size:12px">${esc(fill)}</td>
            <td style="padding:9px 12px;color:#8b5cf6;font-size:12px">${esc(rels)}</td>
        </tr>`;
    }).join('');

    // ── Controllers table ────────────────────────────────────────────────────
    const ctrlRows = (d.controllers ?? []).slice(0, 30).map(c => {
        const barW = Math.min(120, Math.round((c.method_count ?? 0) * 8));
        const barColor = (c.method_count ?? 0) > 15 ? '#ef4444' : (c.method_count ?? 0) > 10 ? '#f59e0b' : '#10b981';
        return `<tr>
            <td style="padding:9px 12px;font-weight:700;color:#1e293b;font-family:ui-monospace,monospace;font-size:13px">${esc(c.name)}</td>
            <td style="padding:9px 12px">
                <div style="display:flex;align-items:center;gap:8px">
                    <div style="width:${barW}px;height:8px;background:${barColor};border-radius:4px;opacity:0.8"></div>
                    <span style="font-size:12px;font-weight:700;color:${barColor}">${c.method_count ?? 0}</span>
                </div>
            </td>
            <td style="padding:9px 12px;font-size:12px;color:#64748b;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc((c.methods ?? []).slice(0,8).join(', '))}</td>
        </tr>`;
    }).join('');

    // ── Routes table ─────────────────────────────────────────────────────────
    const routeRows = (d.routes ?? []).slice(0, 50).map(r => {
        const methods = (r.methods ?? []).filter(m => m !== 'HEAD').join('|');
        const ctrl    = (r.controller?.class ?? '—').split('\\').pop();
        const action  = r.controller?.method ?? '—';
        const mwShort = (r.middleware ?? []).map(m => m.split('\\').pop()).slice(0, 2).join(', ');
        const mCol    = methodColors[methods] ?? '#64748b';
        return `<tr>
            <td style="padding:8px 12px"><span style="font-size:11px;font-weight:700;color:${mCol};background:${mCol}18;padding:2px 8px;border-radius:6px;font-family:ui-monospace,monospace">${esc(methods)}</span></td>
            <td style="padding:8px 12px;font-family:ui-monospace,monospace;font-size:12px;color:#1e293b;font-weight:600">${esc(r.uri)}</td>
            <td style="padding:8px 12px;font-family:ui-monospace,monospace;font-size:11px;color:#64748b">${esc(ctrl)}@${esc(action)}</td>
            <td style="padding:8px 12px;font-size:11px;color:#94a3b8">${esc(mwShort)}</td>
        </tr>`;
    }).join('');

    // ── Section header helper ─────────────────────────────────────────────────
    const secHeader = (title, sub, color = '#4f46e5') =>
        `<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
            <div style="width:4px;height:32px;border-radius:2px;background:${color};flex-shrink:0"></div>
            <div>
                <h2 style="margin:0;font-size:20px;font-weight:800;color:#0f172a;font-family:system-ui,sans-serif">${esc(title)}</h2>
                ${sub ? `<p style="margin:2px 0 0;font-size:13px;color:#64748b;font-family:system-ui,sans-serif">${esc(sub)}</p>` : ''}
            </div>
        </div>`;

    const tableWrap = (headers, rows) =>
        `<div style="border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;background:#ffffff">
            <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif">
                <thead><tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
                    ${headers.map(h => `<th style="padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em">${esc(h)}</th>`).join('')}
                </tr></thead>
                <tbody style="divide-y:1px solid #f1f5f9">${rows}</tbody>
            </table>
        </div>`;

    // ── Assemble full HTML ────────────────────────────────────────────────────
    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Architecture Report — ${esc(proj)}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f8fafc;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;line-height:1.5}
a{color:inherit;text-decoration:none}
table tr:nth-child(even){background:#fafbfc}
table tr:hover{background:#f1f5f9}
@media print{body{background:#fff}.no-print{display:none}}
</style>
</head>
<body>

<!-- ═══ HEADER ═══════════════════════════════════════════════════════════ -->
<div style="background:linear-gradient(135deg,#1e293b 0%,#312e81 100%);color:#fff;padding:40px 48px;display:flex;align-items:center;justify-content:space-between;gap:20px">
    <div>
        <p style="font-size:12px;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;margin-bottom:6px">Architecture Report</p>
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px">${esc(proj)}</h1>
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:8px">
            <span style="font-size:13px;color:#94a3b8">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:#94a3b8">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:#94a3b8">Generated: ${esc(d.generated_at ?? '')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:20px;padding:20px 28px;flex-shrink:0">
        <div style="font-size:48px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:14px;font-weight:700;color:#a78bfa;margin-top:4px">${esc(grade)}</div>
        <div style="font-size:11px;color:#64748b;margin-top:2px">Architecture Score</div>
    </div>
</div>

<!-- ═══ BODY ═════════════════════════════════════════════════════════════ -->
<div style="max-width:1200px;margin:0 auto;padding:40px 32px;display:flex;flex-direction:column;gap:48px">

    <!-- Stat Cards -->
    <section>
        ${secHeader('Component Overview', `${(d.models??[]).length} models · ${(d.controllers??[]).length} controllers · ${rs.total??0} routes`)}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px">
            ${statCards}
        </div>
    </section>

    <!-- Score -->
    <section>
        ${secHeader('Architecture Score', `${score}/100 — ${esc(grade)}`, '#10b981')}
        <div style="display:grid;grid-template-columns:160px 1fr;gap:24px;align-items:start;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px">
            ${gaugeSvg}
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif">
                    <tbody>${checkRows || '<tr><td colspan="3" style="padding:12px;color:#94a3b8;font-size:13px">No score checks available.</td></tr>'}</tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Routes -->
    <section>
        ${secHeader('Routes', `${rs.total??0} total · ${rs.web??0} web · ${rs.api??0} API · ${rs.named_count??0} named`, '#10b981')}
        <div style="display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px">
                <p style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">By Method</p>
                ${routeBarsSvg}
            </div>
            <div style="overflow-x:auto">
                ${tableWrap(['Method','URI','Handler','Middleware'], routeRows || '<tr><td colspan="4" style="padding:12px;color:#94a3b8">No routes.</td></tr>')}
            </div>
        </div>
    </section>

    <!-- Dependency Graph -->
    ${depSvg ? `<section>
        ${secHeader('Dependency Graph', `${depNodes.length} nodes · ${depEdges.length} edges`, '#6366f1')}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden">
            <div style="overflow-x:auto;padding:20px">${depSvg}</div>
        </div>
    </section>` : ''}

    <!-- Models -->
    <section>
        ${secHeader('Models', `${(d.models??[]).length} Eloquent models detected`, '#8b5cf6')}
        ${tableWrap(['Model','Table','Fillable Fields','Relationships'], modelRows || '<tr><td colspan="4" style="padding:12px;color:#94a3b8">No models.</td></tr>')}
    </section>

    <!-- Controllers -->
    <section>
        ${secHeader('Controllers', `${(d.controllers??[]).length} controllers`, '#3b82f6')}
        ${tableWrap(['Controller','Methods','Method List'], ctrlRows || '<tr><td colspan="3" style="padding:12px;color:#94a3b8">No controllers.</td></tr>')}
    </section>

</div>

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════ -->
<div style="background:#1e293b;color:#64748b;text-align:center;padding:24px;font-size:12px;font-family:system-ui,sans-serif;margin-top:20px">
    Generated by <strong style="color:#94a3b8">laravel-architecture-discovery</strong> · ${esc(d.generated_at ?? '')}
</div>

</body>
</html>`;
}

function _buildDepSvg(nodes, edges) {
    if (!nodes.length) return '';

    const layerOrder = ['controller','job','event','listener','service','repository','model'];
    const layerColors = {
        controller: { fill:'#EAF2FF', stroke:'#0052CC', text:'#172B4D' },
        service:    { fill:'#E3FCEF', stroke:'#00875A', text:'#172B4D' },
        repository: { fill:'#FFFAE6', stroke:'#FF8B00', text:'#172B4D' },
        model:      { fill:'#F3F0FF', stroke:'#6554C0', text:'#172B4D' },
        job:        { fill:'#FFF4E5', stroke:'#FF5630', text:'#172B4D' },
        event:      { fill:'#FFF0FB', stroke:'#BF40BF', text:'#172B4D' },
        listener:   { fill:'#FEE4FA', stroke:'#DA62AC', text:'#172B4D' },
    };

    const NW = 140, NH = 56, GAP_X = 18, GAP_Y = 90, PAD = 30;
    const byLayer = {};
    nodes.forEach(n => {
        const l = n.layer ?? 'model';
        (byLayer[l] = byLayer[l] || []).push(n);
    });

    const lKeys = layerOrder.filter(l => byLayer[l]?.length);
    const maxW  = Math.max(...lKeys.map(l => byLayer[l].length * NW + (byLayer[l].length - 1) * GAP_X));
    const CW    = Math.max(maxW + PAD * 2, 480);
    const CH    = lKeys.length * (NH + GAP_Y) - GAP_Y + PAD * 2;

    const nameToPos = {};
    lKeys.forEach((l, li) => {
        const row  = byLayer[l];
        const rowW = row.length * NW + (row.length - 1) * GAP_X;
        let x = (CW - rowW) / 2;
        const y = PAD + li * (NH + GAP_Y);
        row.forEach(n => {
            nameToPos[n.name] = { x, y, cx: x + NW / 2, cy: y + NH / 2 };
            x += NW + GAP_X;
        });
    });

    const edgesSvg = edges.slice(0, 120).map(e => {
        const f = nameToPos[e.from], t = nameToPos[e.to];
        if (!f || !t) return '';
        const x1 = f.cx, y1 = f.y + NH, x2 = t.cx, y2 = t.y;
        const cp = Math.abs(y2 - y1) * 0.4;
        return `<path d="M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}"
            fill="none" stroke="rgba(148,178,222,0.35)" stroke-width="1.5" marker-end="url(#dep-arr)"/>`;
    }).join('');

    const nodesSvg = nodes.map(n => {
        const p = nameToPos[n.name]; if (!p) return '';
        const c  = layerColors[n.layer ?? ''] ?? { fill:'#F4F5F7', stroke:'#6B778C', text:'#172B4D' };
        const nm = n.name.length > 18 ? n.name.slice(0, 17) + '…' : n.name;
        const lb = (n.layer ?? '').toUpperCase();
        return `<g>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="10" fill="${c.fill}" stroke="${c.stroke}" stroke-width="1.5"/>
            <text x="${p.cx}" y="${p.y + 22}" text-anchor="middle" font-size="10" font-weight="700" fill="${c.stroke}" font-family="ui-monospace,monospace">${lb}</text>
            <text x="${p.cx}" y="${p.y + 40}" text-anchor="middle" font-size="11" font-weight="600" fill="${c.text}" font-family="ui-monospace,monospace">${nm}</text>
        </g>`;
    }).join('');

    return `<svg width="${CW}" height="${CH}" style="display:block;max-width:100%">
        <defs>
            <pattern id="dep-dot" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <circle cx="1" cy="1" r="0.7" fill="rgba(148,178,222,0.1)"/>
            </pattern>
            <marker id="dep-arr" markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto">
                <polygon points="0 0,8 3,0 6" fill="#6B778C"/>
            </marker>
        </defs>
        <rect width="${CW}" height="${CH}" fill="#F7F8F9"/>
        <rect width="${CW}" height="${CH}" fill="url(#dep-dot)"/>
        ${edgesSvg}
        ${nodesSvg}
    </svg>`;
}

// ── AI Insights ───────────────────────────────────────────────────────────────

const AI_ENDPOINT = '{{ route("architecture.ai.analyze") }}';
const AI_CSRF     = '{{ csrf_token() }}';

async function aiAnalyze() {
    document.getElementById('ai-loading').style.display = 'block';
    document.getElementById('ai-error').style.display = 'none';
    document.getElementById('ai-results').style.display = 'none';
    const trigEl = document.getElementById('ai-trigger');
    if (trigEl) { trigEl.style.opacity = '0.5'; trigEl.style.pointerEvents = 'none'; }

    try {
        const res = await fetch(AI_ENDPOINT, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  AI_CSRF,
            },
        });

        const json = await res.json();

        if (!res.ok || json.error) {
            throw new Error(json.error || 'Server returned ' + res.status);
        }

        aiRenderResults(json);
    } catch (err) {
        document.getElementById('ai-error-msg').textContent = err.message;
        document.getElementById('ai-error').style.display = 'block';
    } finally {
        document.getElementById('ai-loading').style.display = 'none';
        if (trigEl) { trigEl.style.opacity = ''; trigEl.style.pointerEvents = ''; }
    }
}

function aiRenderResults(data) {
    // Summary
    document.getElementById('ai-summary').textContent = data.summary || 'No summary available.';

    // Score
    const score = data.score || 0;
    document.getElementById('ai-score-num').textContent = score;
    setTimeout(() => {
        document.getElementById('ai-score-bar').style.width = score + '%';
    }, 50);

    // SOLID
    const solidEl = document.getElementById('ai-solid');
    solidEl.innerHTML = '';
    const solidNames = { S: 'Single Resp.', O: 'Open/Closed', L: 'Liskov Sub.', I: 'Interface Seg.', D: 'Dep. Inversion' };
    Object.entries(data.solid_review || {}).forEach(([key, val]) => {
        const color = val.status === 'pass' ? 'green' : val.status === 'warn' ? 'amber' : 'red';
        const icon  = val.status === 'pass' ? '✔' : val.status === 'warn' ? '⚠' : '✘';
        solidEl.insertAdjacentHTML('beforeend', `
            <div class="flex flex-col items-center text-center p-3 rounded-xl bg-${color}-50 border border-${color}-200">
                <span class="text-xl font-bold text-${color}-600">${key}</span>
                <span class="text-xs font-medium text-${color}-700 mt-0.5">${solidNames[key] || ''}</span>
                <span class="text-lg mt-2">${icon}</span>
                <p class="text-xs text-${color}-600 mt-1 leading-tight">${_esc(val.note || '')}</p>
            </div>
        `);
    });

    // Problems
    const problemsEl = document.getElementById('ai-problems');
    problemsEl.innerHTML = '';
    if (!(data.problems || []).length) {
        problemsEl.innerHTML = '<p class="text-sm text-slate-400 italic">No problems detected.</p>';
    }
    (data.problems || []).forEach(p => {
        const sev   = p.severity || 'info';
        const color = sev === 'error' ? 'red' : sev === 'warning' ? 'amber' : 'blue';
        problemsEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg bg-${color}-50 border border-${color}-100">
                <div class="shrink-0 mt-0.5">
                    <span class="inline-block px-1.5 py-0.5 text-xs font-bold rounded uppercase bg-${color}-100 text-${color}-700">${sev}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">${_esc(p.title || '')}</p>
                    ${p.location ? `<p class="text-xs text-slate-500 mt-0.5">📍 ${_esc(p.location)}</p>` : ''}
                    <p class="text-sm text-slate-600 mt-1">${_esc(p.description || '')}</p>
                </div>
            </div>
        `);
    });

    // Suggestions
    const suggEl = document.getElementById('ai-suggestions');
    suggEl.innerHTML = '';
    if (!(data.suggestions || []).length) {
        suggEl.innerHTML = '<p class="text-sm text-slate-400 italic">No suggestions.</p>';
    }
    (data.suggestions || []).forEach(s => {
        const pri   = s.priority || 'medium';
        const color = pri === 'high' ? 'red' : pri === 'medium' ? 'amber' : 'slate';
        suggEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50">
                <span class="shrink-0 mt-0.5 inline-block px-1.5 py-0.5 h-fit text-xs font-bold rounded uppercase bg-${color}-100 text-${color}-700">${pri}</span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">${_esc(s.title || '')}</p>
                    <p class="text-sm text-slate-600 mt-0.5">${_esc(s.description || '')}</p>
                    ${s.example ? `<code class="block mt-1.5 text-xs bg-slate-800 text-green-300 px-2 py-1 rounded">${_esc(s.example)}</code>` : ''}
                </div>
            </div>
        `);
    });

    // Laravel Best Practices
    const laravelEl = document.getElementById('ai-laravel-practices');
    laravelEl.innerHTML = '';
    (data.laravel_best_practices || []).forEach(p => {
        const icon  = p.status === 'pass' ? '✔' : p.status === 'warn' ? '⚠' : '✘';
        const color = p.status === 'pass' ? 'green' : p.status === 'warn' ? 'amber' : 'red';
        laravelEl.insertAdjacentHTML('beforeend', `
            <div class="flex items-start gap-2.5 py-1.5 border-b border-slate-100 last:border-0">
                <span class="text-${color}-600 font-bold mt-0.5 shrink-0">${icon}</span>
                <div class="min-w-0">
                    <span class="text-sm font-medium text-slate-700">${_esc(p.name || '')}</span>
                    ${p.note ? `<span class="text-xs text-slate-500 ml-2">${_esc(p.note)}</span>` : ''}
                </div>
            </div>
        `);
    });

    // Already followed best practices
    const bpEl = document.getElementById('ai-best-practices');
    bpEl.innerHTML = '';
    (data.best_practices || []).forEach(bp => {
        bpEl.insertAdjacentHTML('beforeend', `
            <li class="flex items-start gap-2 text-sm text-slate-600">
                <span class="text-green-500 shrink-0 mt-0.5">✔</span>
                <span>${_esc(bp)}</span>
            </li>
        `);
    });

    // Provider badge
    document.getElementById('ai-provider-badge').textContent =
        'Analyzed by ' + (data.provider || 'AI') + ' · ' + (data.model || '');

    document.getElementById('ai-results').style.display = 'block';
}

function _esc(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _downloadBlob(content, filename, mime, isBlob = false) {
    const blob = isBlob ? content : new Blob([content], { type: mime });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(() => URL.revokeObjectURL(url), 2000);
}
</script>
</body>
</html>
