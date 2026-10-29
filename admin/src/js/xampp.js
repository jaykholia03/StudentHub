/* Lightweight interactions for the PHP/XAMPP build; no npm or module bundler needed. */
(function(){
 // Theme toggle shared with the student-facing pages.
 const themeToggle=document.getElementById('adminThemeToggle');
 // Restore the saved theme before wiring the toggle.
 try { if (localStorage.getItem('theme') === 'dark') document.body.classList.add('dark'); } catch(e) {}
 function syncThemeButton(){
   if(!themeToggle) return;
   const dark=document.body.classList.contains('dark');
   themeToggle.innerHTML=dark ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-stars-fill"></i>';
   themeToggle.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
   themeToggle.setAttribute('title', dark ? 'Switch to light mode' : 'Switch to dark mode');
 }
 syncThemeButton();
 if(themeToggle) themeToggle.addEventListener('click',()=>{
   const dark=!document.body.classList.contains('dark');
   document.body.classList.toggle('dark', dark);
   document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
   try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch(e) {}
   syncThemeButton();
 });
 const sidebar=document.getElementById('sidebar');
 const wrapper=document.getElementById('mainWrapper');
 const toggle=document.getElementById('sidebarToggle');
 const close=document.getElementById('sidebarClose');
 const overlay=document.getElementById('sidebarOverlay');
 const mobile=()=>window.innerWidth<=768;
 function open(){if(sidebar){sidebar.classList.add('active');} if(overlay) overlay.classList.add('active');}
 function shut(){if(sidebar) sidebar.classList.remove('active'); if(overlay) overlay.classList.remove('active');}
 if(toggle) toggle.addEventListener('click',()=>{ if(mobile()) { sidebar && sidebar.classList.contains('active') ? shut() : open(); } else { sidebar && sidebar.classList.toggle('collapsed'); wrapper && wrapper.classList.toggle('full-width'); }});
 if(close) close.addEventListener('click',shut); if(overlay) overlay.addEventListener('click',shut);
 const searchToggle=document.getElementById('searchToggle'); const searchContainer=document.querySelector('.search-container');
 if(searchToggle&&searchContainer) searchToggle.addEventListener('click',()=>{searchContainer.classList.toggle('search-open'); const input=document.getElementById('searchInput'); if(input) input.focus();});
 const searchForm=document.getElementById('searchForm'); if(searchForm) searchForm.addEventListener('submit',e=>{e.preventDefault(); const q=(document.getElementById('searchInput')?.value||'').trim().toLowerCase(); if(!q)return; const links=[...document.querySelectorAll('.sidebar a')]; const match=links.find(a=>a.textContent.toLowerCase().includes(q)); if(match && match.href && !match.href.endsWith('#')) location.href=match.href; else alert('No matching menu item found.');});
 // Mark the current sidebar page and open its containing submenu.
 const current=location.pathname.split('/').pop(); document.querySelectorAll('.sidebar a[href]').forEach(a=>{if(a.getAttribute('href')===current){a.classList.add('active'); const submenu=a.closest('.submenu'); if(submenu){submenu.classList.add('show'); const trigger=document.querySelector('[data-bs-target="#'+submenu.id+'"]'); if(trigger){trigger.classList.add('active');trigger.setAttribute('aria-expanded','true');}}}});
})();
