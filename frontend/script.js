document.addEventListener('DOMContentLoaded', () => {
    
    // Globale Config nutzen oder Fallback
    const CONFIG = (typeof HOCHZEITSTAG_CONFIG !== 'undefined') ? HOCHZEITSTAG_CONFIG : {
        weddingDate: "2025-09-06T11:02:00",
        firstContactDate: "2014-01-11T19:02:00",
        firstMeetDate: "2014-04-01T21:02:00",
        backgroundImage: "",
        quotes: ["Liebe ist alles."]
    };

    // Background Image Logic
    if (CONFIG.backgroundImage) {
        // Handled by CSS/HTML usually, but keeping config logic if needed later
    }
    
    // Theme Color Logic
    if (CONFIG.themeColor) {
        document.documentElement.style.setProperty('--primary-color', CONFIG.themeColor);
    }

    const WEDDING_DATE = new Date(CONFIG.weddingDate);
    
    const historyEvents = [
        { name: "Erster Kontakt", date: CONFIG.firstContactDate },
        { name: "Zusammen", date: CONFIG.firstMeetDate },
        { name: "Hochzeit", date: CONFIG.weddingDate }
    ];

    // DOM Elements
    const elYears = document.getElementById('val-years');
    const elDays = document.getElementById('val-days');
    const elHours = document.getElementById('val-hours');
    const elMinutes = document.getElementById('val-minutes');
    const elTotalSeconds = document.getElementById('total-seconds');
    const elTotalDays = document.getElementById('total-days');
    const elQuoteDisplay = document.getElementById('quote-display');
    const elSurpriseDisplay = document.getElementById('surprise-display');
    const elWeddingDateDisplay = document.getElementById('wedding-date-display');
    const elTestEmailBtn = document.getElementById('test-email-button');
    const elEmailScheduleInfo = document.getElementById('email-schedule-info');
    
    const elMilestoneList = document.getElementById('milestone-list');
    const elHistoryList = document.getElementById('history-list');
    const elNextAnniversary = document.getElementById('next-anniversary');
    const elLocalTime = document.getElementById('local-time-display');

    function updateClock() {
        if (!elLocalTime) return;
        const now = new Date();
        const timeStr = now.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const dateStr = now.toLocaleDateString('de-DE');
        elLocalTime.innerText = `Zeit: ${dateStr} ${timeStr}`;
    }

    // --- FIREWORKS LOGIC ---
    let fireworksCanvas, ctx, fireworks = [], particles = [];
    let fireworksActive = false;

    function initFireworks() {
        if (document.getElementById('fireworks-canvas')) return;
        fireworksCanvas = document.createElement('canvas');
        fireworksCanvas.id = 'fireworks-canvas';
        document.body.appendChild(fireworksCanvas);
        ctx = fireworksCanvas.getContext('2d');
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        loopFireworks();
    }

    function resizeCanvas() {
        if(!fireworksCanvas) return;
        fireworksCanvas.width = window.innerWidth;
        fireworksCanvas.height = window.innerHeight;
    }

    function random(min, max) { return Math.random() * (max - min) + min; }

    class Particle {
        constructor(x, y, hue) {
            this.x = x;
            this.y = y;
            this.coordinates = [];
            this.coordinateCount = 5;
            while (this.coordinateCount--) {
                this.coordinates.push([this.x, this.y]);
            }
            this.angle = random(0, Math.PI * 2);
            this.speed = random(1, 10);
            this.friction = 0.95;
            this.gravity = 1;
            this.hue = random(hue - 20, hue + 20);
            this.brightness = random(50, 80);
            this.alpha = 1;
            this.decay = random(0.015, 0.03);
        }

        update(index) {
            this.coordinates.pop();
            this.coordinates.unshift([this.x, this.y]);
            this.speed *= this.friction;
            this.x += Math.cos(this.angle) * this.speed;
            this.y += Math.sin(this.angle) * this.speed + this.gravity;
            this.alpha -= this.decay;
            if (this.alpha <= this.decay) {
                particles.splice(index, 1);
            }
        }

        draw() {
            ctx.beginPath();
            ctx.moveTo(this.coordinates[this.coordinates.length - 1][0], this.coordinates[this.coordinates.length - 1][1]);
            ctx.lineTo(this.x, this.y);
            ctx.strokeStyle = 'hsla(' + this.hue + ', 100%, ' + this.brightness + '%, ' + this.alpha + ')';
            ctx.stroke();
        }
    }

    class Firework {
        constructor(sx, sy, tx, ty) {
            this.x = sx;
            this.y = sy;
            this.sx = sx;
            this.sy = sy;
            this.tx = tx;
            this.ty = ty;
            this.distanceToTarget = Math.sqrt(Math.pow(tx - sx, 2) + Math.pow(ty - sy, 2));
            this.distanceTraveled = 0;
            this.coordinates = [];
            this.coordinateCount = 3;
            while (this.coordinateCount--) {
                this.coordinates.push([this.x, this.y]);
            }
            this.angle = Math.atan2(ty - sy, tx - sx);
            this.speed = 2;
            this.acceleration = 1.05;
            this.brightness = random(50, 70);
            this.targetRadius = 1;
        }

        update(index) {
            this.coordinates.pop();
            this.coordinates.unshift([this.x, this.y]);
            this.speed *= this.acceleration;
            const vx = Math.cos(this.angle) * this.speed;
            const vy = Math.sin(this.angle) * this.speed;
            this.distanceTraveled = Math.sqrt(Math.pow(this.sx - this.x, 2) + Math.pow(this.sy - this.y, 2));

            if (this.distanceTraveled >= this.distanceToTarget) {
                createParticles(this.tx, this.ty);
                fireworks.splice(index, 1);
            } else {
                this.x += vx;
                this.y += vy;
            }
        }

        draw() {
            ctx.beginPath();
            ctx.moveTo(this.coordinates[this.coordinates.length - 1][0], this.coordinates[this.coordinates.length - 1][1]);
            ctx.lineTo(this.x, this.y);
            ctx.strokeStyle = 'hsl(' + random(0, 360) + ', 100%, ' + this.brightness + '%)';
            ctx.stroke();
        }
    }

    function createParticles(x, y) {
        let particleCount = 30;
        let hue = random(0, 360);
        while (particleCount--) {
            particles.push(new Particle(x, y, hue));
        }
    }

    function loopFireworks() {
        if (!fireworksActive) return;
        requestAnimationFrame(loopFireworks);
        
        ctx.globalCompositeOperation = 'destination-out';
        ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
        ctx.fillRect(0, 0, fireworksCanvas.width, fireworksCanvas.height);
        ctx.globalCompositeOperation = 'lighter';

        let i = fireworks.length;
        while (i--) {
            fireworks[i].draw();
            fireworks[i].update(i);
        }

        let j = particles.length;
        while (j--) {
            particles[j].draw();
            particles[j].update(j);
        }

        if (Math.random() < 0.05) { // Spawn rate
            fireworks.push(new Firework(fireworksCanvas.width / 2, fireworksCanvas.height, random(0, fireworksCanvas.width), random(0, fireworksCanvas.height / 2)));
        }
    }

    function startFireworks() {
        if (fireworksActive) return;
        fireworksActive = true;
        initFireworks();
    }

    /* --- Hilfsfunktionen --- */

    function displayRandomQuote() {
        if (!elQuoteDisplay || !CONFIG.quotes || CONFIG.quotes.length === 0) return;
        
        let randomIndex;
        if (CONFIG.quotes.length > 1) {
            const lastIndex = parseInt(localStorage.getItem('hochzeitstag_last_quote_index'));
            // Try to find a new index (max 10 attempts to be safe)
            let attempts = 0;
            do {
                randomIndex = Math.floor(Math.random() * CONFIG.quotes.length);
                attempts++;
            } while (randomIndex === lastIndex && attempts < 10);
        } else {
            randomIndex = 0;
        }
        
        localStorage.setItem('hochzeitstag_last_quote_index', randomIndex);
        elQuoteDisplay.innerHTML = `„${CONFIG.quotes[randomIndex]}“`;
    }

    function displayRandomSurprise() {
        if (!elSurpriseDisplay || !CONFIG.surpriseIdeas || CONFIG.surpriseIdeas.length === 0) return;
        
        let randomIndex;
        if (CONFIG.surpriseIdeas.length > 1) {
             const lastIndex = parseInt(localStorage.getItem('hochzeitstag_last_surprise_index'));
             let attempts = 0;
             do {
                 randomIndex = Math.floor(Math.random() * CONFIG.surpriseIdeas.length);
                 attempts++;
             } while (randomIndex === lastIndex && attempts < 10);
        } else {
            randomIndex = 0;
        }

        localStorage.setItem('hochzeitstag_last_surprise_index', randomIndex);
        elSurpriseDisplay.innerHTML = `💡 ${CONFIG.surpriseIdeas[randomIndex]}`;
    }

    function formatShortDate(date) {
        return date.toLocaleDateString('de-DE', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }

    function getRandomIdeas(count) {
        if (!CONFIG.surpriseIdeas) return [];
        const shuffled = [...CONFIG.surpriseIdeas].sort(() => 0.5 - Math.random());
        return shuffled.slice(0, count);
    }

    // Zeig nur Tage an (0 = Heute, >0 = Zukunft, null = Vergangenheit)
    function getRemainingDays(targetDate) {
        const now = new Date();
        const t = new Date(targetDate);
        
        // Normalize both to midnight for day calculation
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const target = new Date(t.getFullYear(), t.getMonth(), t.getDate());
        
        const diffMs = target - today;
        const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));
        
        if (diffDays < 0) return null; 
        return diffDays;
    }

    function calculateTimeComponents(startDate, endDate) {
        let diff = endDate - startDate;
        if (diff < 0) diff = 0;

        const totalSeconds = Math.floor(diff / 1000);
        const totalDays = Math.floor(totalSeconds / (3600 * 24));
        
        let tempDate = new Date(startDate);
        let years = 0;
        while(true) {
            tempDate.setFullYear(tempDate.getFullYear() + 1);
            if(tempDate > endDate) {
                tempDate.setFullYear(tempDate.getFullYear() - 1);
                break;
            }
            years++;
        }
        const diffRest = endDate - tempDate;
        const days = Math.floor(diffRest / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diffRest % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diffRest % (1000 * 60 * 60)) / (1000 * 60));

        return { years, days, hours, minutes, totalSeconds, totalDays };
    }

    /* --- Haupt-Update Funktion --- */
    function updateTimer() {
        const now = new Date();
        const time = calculateTimeComponents(WEDDING_DATE, now);
        updateClock();

        // Header Counters
        if (elYears) elYears.innerText = time.years;
        if (elDays) elDays.innerText = time.days;
        if (elHours) elHours.innerText = time.hours;
        if (elMinutes) elMinutes.innerText = time.minutes;
        
        // Footer Stats
        if (elTotalSeconds) elTotalSeconds.innerText = time.totalSeconds.toLocaleString('de-DE');
        if (elTotalDays) elTotalDays.innerText = time.totalDays.toLocaleString('de-DE');

        // Startdatum Info
        if (elWeddingDateDisplay) {
            elWeddingDateDisplay.innerText = `Unsere Reise begann am ${formatShortDate(WEDDING_DATE)}`;
        }

        // --- Render HISTORY Timeline ---
        if (elHistoryList) {
            const allHistoryEvents = [...historyEvents]; // Start with base events

            // Add past wedding anniversaries
            const weddingYear = WEDDING_DATE.getFullYear();
            const currentYear = now.getFullYear();

            for (let y = weddingYear; y <= currentYear; y++) {
                let annivDate = new Date(WEDDING_DATE);
                annivDate.setFullYear(y);

                // Only add if the anniversary date has passed (or is today)
                // And it's not the actual wedding day itself (which is already in historyEvents)
                if (annivDate <= now && y !== weddingYear) {
                    const num = y - weddingYear;
                    allHistoryEvents.push({ name: `${num}. Hochzeitstag`, date: annivDate.toISOString() });
                }
            }

            // Sort all history events chronologically
            allHistoryEvents.sort((a, b) => new Date(a.date) - new Date(b.date));

            let html = '';
            allHistoryEvents.forEach(evt => {
                const d = new Date(evt.date);
                const t = calculateTimeComponents(d, now);
                
                // Adjust countdown for events in the past, show "vor X Jahren"
                const countdownText = t.years > 0 ? `vor ${t.years} Jahren, ${t.days} Tagen` : `vor ${t.days} Tagen`;

                html += `
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="t-label">${evt.name}</span>
                        <span class="t-date">${formatShortDate(d)}</span>
                        <span class="t-countdown">${countdownText}</span>
                    </div>
                </div>`;
            });
            elHistoryList.innerHTML = html;
        }

        // --- Render MILESTONES Timeline ---
        if (elMilestoneList) {
            const milestones = [];
            const oneDayMs = 86400000;
            const daysPassed = Math.floor((now - WEDDING_DATE) / oneDayMs);

            // Helper for adding annual events (Anniversaries, Birthdays)
            const addAnnualEvents = (date, labelBase, type) => {
                 const currentYear = now.getFullYear();
                 // Check this year and next few years
                 for(let y = currentYear; y <= currentYear + 3; y++) {
                     let evtDate = new Date(date);
                     evtDate.setFullYear(y);
                     
                     if (getRemainingDays(evtDate) < 0) continue;
                     
                     let label = labelBase;
                     if (type === 'wedding') {
                         const num = y - WEDDING_DATE.getFullYear();
                         if (num <= 0) continue;
                         label = `${num}. Hochzeitstag`;
                     }
                     milestones.push({ date: evtDate, label: label });
                 }
            };

            // 1. Hochzeitstage
            addAnnualEvents(WEDDING_DATE, "Hochzeitstag", 'wedding');

            // 2. Geburtstage
            if (CONFIG.birthdays) {
                for (const [name, dateStr] of Object.entries(CONFIG.birthdays)) {
                     if(dateStr) {
                         const bday = new Date(dateStr);
                         const formattedName = name.charAt(0).toUpperCase() + name.slice(1);
                         addAnnualEvents(bday, `Geburtstag ${formattedName}`, 'birthday');
                     }
                }
            }
            
            // 2b. Custom Events
            if (CONFIG.customEvents && Array.isArray(CONFIG.customEvents)) {
                CONFIG.customEvents.forEach(evt => {
                     if (evt.date && evt.label) {
                         const d = new Date(evt.date);
                         addAnnualEvents(d, `Special Event: ${evt.label}`, 'custom');
                     }
                });
            }

            // 3. Vierteljahre (1/4, 1/2, 3/4)
            const currentYear = now.getFullYear();
            const weddingYear = WEDDING_DATE.getFullYear();
            
            for (let y = currentYear; y <= currentYear + 3; y++) {
                // Calculate "wedding year count" for this calendar year
                // If wedding was 2014, and y is 2025.
                // Quarters in 2025 belong to the "10th" or "11th" year depending on month?
                // Actually easier: Iterate quarters relative to Wedding Date
                
                // Let's iterate offset years from wedding
                let diffYear = y - weddingYear;
                
                for (let offset = -1; offset <= 1; offset++) { // Check surrounding years to be safe with month overflow
                    let baseYear = weddingYear + diffYear + offset;
                    if (baseYear < 0) continue;

                    for (let q = 1; q < 4; q++) { // 1=1/4, 2=1/2, 3=3/4. 0 is full year (handled above)
                        let qDate = new Date(WEDDING_DATE);
                        qDate.setFullYear(baseYear);
                        qDate.setMonth(WEDDING_DATE.getMonth() + (q * 3));
                        
                        if (getRemainingDays(qDate) === null) continue; // Past or today
                        
                        // Calculate label (how many years passed?)
                        // Technically: baseYear - weddingYear + fraction?
                        // If baseYear=2025 (11 years after 2014).
                        // Date is e.g. Dec 2025.
                        // Full years passed: 2025-2014 = 11.
                        // So "11 1/4 Jahre".
                        
                        // Check exact logic:
                        // Wedding: Sept 2014.
                        // q=1 (+3m) -> Dec 2014. (0 1/4 years).
                        // So year 2025 (+11) -> Dec 2025 -> 11 1/4 years.
                        // Correct.
                        
                        let yearsLabel = baseYear - weddingYear;
                        
                        // If the month addition pushed it to next year, does setMonth handle it? Yes.
                        // But does it affect our label logic?
                        // qDate is the actual date.
                        // We strictly want "X 1/4 Years".
                        // So we use the year we SET (baseYear) as the X.
                        // Example: Wedding Dec 2020.
                        // q=1 (+3m) -> March 2021.
                        // baseYear 2020. qDate = March 2021.
                        // Label: "0 1/4 Jahre". Correct.
                        
                        const fraction = q === 1 ? "1/4" : (q === 2 ? "1/2" : "3/4");
                        let label = `${yearsLabel} ${fraction} Jahre`;
                        if (yearsLabel === 0) label = `${fraction} Jahr`;
                        
                        milestones.push({ date: qDate, label: label });
                    }
                }
            }

            // 4. Schnapszahlen
            const startDay = Math.max(0, daysPassed);
            for(let digits=3; digits<=5; digits++) {
                for(let n=1; n<=9; n++) {
                    let num = parseInt(String(n).repeat(digits));
                    if(num > startDay) {
                        const date = new Date(WEDDING_DATE.getTime() + (num * oneDayMs));
                        milestones.push({ date: date, label: `${num}. Tag` });
                    }
                }
            }

            // 5. Sortieren
            milestones.sort((a,b) => a.date - b.date);

            // 6. Filtern (nur Zukunft, Limit 5, Unique)
            const uniqueMilestones = [];
            const seen = new Set();
            
            for (const m of milestones) {
                const dLeft = getRemainingDays(m.date);
                if (dLeft === null || dLeft < 0) continue; 
                
                const key = m.date.toDateString() + m.label;
                if (!seen.has(key)) {
                    uniqueMilestones.push(m);
                    seen.add(key);
                }
                if (uniqueMilestones.length >= 5) break;
            }

            // HTML Generieren
            let mHtml = '';
            let isMilestoneToday = false;
            
            uniqueMilestones.forEach(m => {
                const daysLeft = getRemainingDays(m.date);
                if (daysLeft === null) return;
                
                if (daysLeft === 0) isMilestoneToday = true;

                const timeText = daysLeft === 0 ? "Heute!" : `in ${daysLeft} Tagen`;
                
                mHtml += `
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: #fff; border-color: var(--primary-color);"></div>
                    <div class="timeline-content">
                        <span class="t-label">${m.label}</span>
                        <span class="t-date" data-iso="${m.date.toISOString()}">${formatShortDate(m.date)}</span>
                        <span class="t-countdown">${timeText}</span>
                    </div>
                </div>`;
            });
            elMilestoneList.innerHTML = mHtml;

            if (isMilestoneToday) {
                startFireworks();
            }

            // Update Footer Pill
            if (elNextAnniversary) {
                const anniv = uniqueMilestones.find(m => m.label.includes("Hochzeitstag"));
                if (anniv) {
                    const dLeft = getRemainingDays(anniv.date);
                    const txt = dLeft === 0 ? "Heute!" : `Noch ${dLeft} Tage`;
                    elNextAnniversary.innerText = `${txt} bis zum ${anniv.label}`;
                } else if (uniqueMilestones.length > 0) {
                     const first = uniqueMilestones[0];
                     const dLeft = getRemainingDays(first.date);
                     elNextAnniversary.innerText = `Noch ${dLeft} Tage bis zum ${first.label}`;
                } else {
                     elNextAnniversary.innerText = `Keine bevorstehenden Meilensteine`;
                }
            }

            // Update Email Schedule Info
            if (elEmailScheduleInfo) {
                // Calculate next email based on ALL upcoming milestones
                const potentialReminders = [];
                
                uniqueMilestones.forEach(m => {
                    // Reminder 1: 7 days before
                    const r1 = new Date(m.date);
                    r1.setDate(r1.getDate() - (CONFIG.emailReminderDaysFirst || 7));
                    // Set time to something slightly in the future if it's today, to be safe, 
                    // but 'now' comparison handles it.
                    
                    // Reminder 2: 1 day before
                    const r2 = new Date(m.date);
                    r2.setDate(r2.getDate() - (CONFIG.emailReminderDaysSecond || 1));
                    
                    if (r1 > now) potentialReminders.push(r1);
                    if (r2 > now) potentialReminders.push(r2);
                });
                
                // Sort to find the very next one
                potentialReminders.sort((a,b) => a - b);
                
                if (potentialReminders.length > 0) {
                    elEmailScheduleInfo.innerText = `Nächste Mail: ${formatShortDate(potentialReminders[0])}`;
                } else {
                    elEmailScheduleInfo.innerText = "Keine ausstehenden Erinnerungen";
                }
            }
        }
    }

    if (elTestEmailBtn) {
        elTestEmailBtn.addEventListener('click', () => {
             if (typeof hochzeitstag_ajax_object === 'undefined') {
                 alert("Email-Versand funktioniert nur im WordPress Plugin Modus.");
                 return;
             }
             
             // Get next milestone info for the email content from the DOM
             let milestoneLabel = "Ein Meilenstein";
             let milestoneDate = "";
             
             const firstItemLabel = document.querySelector('.timeline-item .t-label');
             const firstItemDate = document.querySelector('.timeline-item .t-date');
             
             if (firstItemLabel) milestoneLabel = firstItemLabel.innerText;
             // Try to get data-iso attribute for robust date parsing in backend
             if (firstItemDate) milestoneDate = firstItemDate.getAttribute('data-iso') || firstItemDate.innerText;

             // Get 5 random ideas
             const ideas = getRandomIdeas(5);

             elTestEmailBtn.disabled = true;
             const originalText = elTestEmailBtn.innerText;
             elTestEmailBtn.innerText = "Sende...";
             
             const data = new FormData();
             data.append('action', 'hochzeitstag_send_test_email');
             data.append('event_label', milestoneLabel); // Override default label
             data.append('event_date', milestoneDate);   // Override default date
             data.append('force_send', 'true');
             
             // Append ideas array
             ideas.forEach((idea, index) => {
                 data.append(`ideas[${index}]`, idea);
             });

             fetch(hochzeitstag_ajax_object.ajax_url, {
                 method: 'POST',
                 body: data
             })
             .then(response => response.json())
             .then(res => {
                 if (res.success) {
                     alert(res.data.message);
                 } else {
                     alert("Fehler: " + (res.data ? res.data.message : 'Unbekannter Fehler'));
                 }
             })
             .catch(err => {
                 alert("Netzwerkfehler beim Senden.");
                 console.error(err);
             })
             .finally(() => {
                 elTestEmailBtn.disabled = false;
                 elTestEmailBtn.innerText = originalText;
             });
        });
    }

    displayRandomQuote();
    displayRandomSurprise();
    updateTimer();
    setInterval(updateTimer, 1000);
});