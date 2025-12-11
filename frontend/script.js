document.addEventListener('DOMContentLoaded', () => {
    /**
     * CONFIGURATION
     * Now loaded from config.js via global object HOCHZEITSTAG_CONFIG
     */
    
    // Apply Background Image from Config
    if (typeof HOCHZEITSTAG_CONFIG !== 'undefined' && HOCHZEITSTAG_CONFIG.backgroundImage) {
        document.documentElement.style.setProperty('--bg-image-url', `url('${HOCHZEITSTAG_CONFIG.backgroundImage}')`);
    }

    const WEDDING_DATE_STR = (typeof HOCHZEITSTAG_CONFIG !== 'undefined') ? HOCHZEITSTAG_CONFIG.weddingDate : "2025-09-06T11:02:00";
    const FIRST_CONTACT_DATE_STR = (typeof HOCHZEITSTAG_CONFIG !== 'undefined') ? HOCHZEITSTAG_CONFIG.firstContactDate : "2014-01-11T19:02:00";
    const FIRST_MEET_DATE_STR = (typeof HOCHZEITSTAG_CONFIG !== 'undefined') ? HOCHZEITSTAG_CONFIG.firstMeetDate : "2014-04-01T21:02:00";
    
    // History Events Configuration
    const historyEvents = [
        { name: "Erster Kontakt", date: FIRST_CONTACT_DATE_STR },
        { name: "Zusammen", date: FIRST_MEET_DATE_STR },
        { name: "Hochzeit", date: WEDDING_DATE_STR }
    ];

    // DOM Elements
    const elYears = document.getElementById('val-years');
    const elDays = document.getElementById('val-days');
    const elHours = document.getElementById('val-hours');
    const elMinutes = document.getElementById('val-minutes');
    const elTotalHours = document.getElementById('total-hours');
    const elTotalSeconds = document.getElementById('total-seconds');
    const elNextAnniversary = document.getElementById('next-anniversary');
    const elQuoteDisplay = document.getElementById('quote-display');
    const elWeddingDateDisplay = document.getElementById('wedding-date-display');
    const elMilestoneList = document.getElementById('milestone-list');
    const elHistoryList = document.getElementById('history-list');

    // List of humorous and affectionate German quotes now in HOCHZEITSTAG_CONFIG

    /**
     * Selects a random quote and displays it.
     */
    function displayRandomQuote() {
        if (!elQuoteDisplay || typeof HOCHZEITSTAG_CONFIG === 'undefined' || !HOCHZEITSTAG_CONFIG.quotes) return;
        const quotes = HOCHZEITSTAG_CONFIG.quotes;
        const randomIndex = Math.floor(Math.random() * quotes.length);
        elQuoteDisplay.innerHTML = quotes[randomIndex].replace(/\n/g, "<br>");
    }

    /**
     * Formats a date object to German string: "Weekday, DD. Month YYYY"
     */
    function formatMilestoneDate(date) {
        const weekday = date.toLocaleDateString('de-DE', { weekday: 'short' }).replace('.', '');
        const datePart = date.toLocaleDateString('de-DE', { day: '2-digit', month: 'short', year: 'numeric' });
        return `${weekday}, ${datePart}`;
    }

    /**
     * Calculates time difference breakdown.
     */
    function calculateTimeComponents(startDate, endDate) {
        let diff = endDate - startDate;
        if (diff < 0) diff = 0;

        const totalSeconds = Math.floor(diff / 1000);
        const totalHours = Math.floor(totalSeconds / 3600);

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

        const diffAfterYears = endDate - tempDate;
        const oneDay = 1000 * 60 * 60 * 24;
        const oneHour = 1000 * 60 * 60;
        const oneMinute = 1000 * 60;
        const oneSecond = 1000;

        const days = Math.floor(diffAfterYears / oneDay);
        const hours = Math.floor((diffAfterYears % oneDay) / oneHour);
        const minutes = Math.floor((diffAfterYears % oneHour) / oneMinute);
        const seconds = Math.floor((diffAfterYears % oneMinute) / oneSecond);

        return { years, days, hours, minutes, seconds, totalHours, totalSeconds };
    }

    /**
     * Calculates remaining time (Total Days, Hours, Min) to a target date.
     */
    function calculateRemainingTime(targetDate) {
        const now = new Date();
        let diff = targetDate - now;
        
        if (diff <= 0) return null; // Date passed

        const oneDay = 1000 * 60 * 60 * 24;
        const oneHour = 1000 * 60 * 60;
        const oneMinute = 1000 * 60;

        // "Summiert Tage" means we don't extract months/years. Just total days.
        const days = Math.floor(diff / oneDay);
        const hours = Math.floor((diff % oneDay) / oneHour);
        const minutes = Math.floor((diff % oneHour) / oneMinute);

        return { days, hours, minutes };
    }

    function updateTimer() {
        const now = new Date();
        const weddingDate = new Date(WEDDING_DATE_STR);

        // Update Main Wedding Timer
        if (elWeddingDateDisplay) {
            const weddingDateFormatted = weddingDate.toLocaleDateString('de-DE', {
                day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });
            elWeddingDateDisplay.innerText = `Start: ${weddingDateFormatted} Uhr`;
        }

        const time = calculateTimeComponents(weddingDate, now);

        if (elYears) elYears.innerText = time.years;
        if (elDays) elDays.innerText = time.days;
        if (elHours) elHours.innerText = time.hours;
        if (elMinutes) elMinutes.innerText = time.minutes;
        if (elTotalHours) elTotalHours.innerText = time.totalHours.toLocaleString('de-DE');
        if (elTotalSeconds) elTotalSeconds.innerText = time.totalSeconds.toLocaleString('de-DE');

        // Update History Table
        if (elHistoryList) {
            let historyHtml = '';
            historyEvents.forEach(event => {
                const eventDate = new Date(event.date);
                const eventTime = calculateTimeComponents(eventDate, now);
                historyHtml += `
                    <tr>
                        <td>${event.name}</td>
                        <td>${eventTime.years}</td>
                        <td>${eventTime.days}</td>
                        <td>${eventTime.hours}</td>
                        <td>${eventTime.minutes}</td>
                        <td>${eventTime.seconds}</td>
                    </tr>
                `;
            });
            elHistoryList.innerHTML = historyHtml;
        }

        // Update Milestones (Besondere Tage) Table
        if (elMilestoneList) {
            const milestones = [];

            // 1. Calculate Day Multiples (every 100 days)
            const oneDayMs = 1000 * 60 * 60 * 24;
            const diffDays = (now - weddingDate) / oneDayMs;
            let startMultiple100 = Math.floor(diffDays / 100) + 1;
            // If strictly negative (future wedding), start at 1
            if (startMultiple100 < 1) startMultiple100 = 1;

            // Generate next 3 100-day milestones
            for (let i = 0; i < 3; i++) {
                const multiple = startMultiple100 + i;
                milestones.push({
                    label: `${multiple * 100}. Tag`,
                    date: new Date(weddingDate.getTime() + (multiple * 100 * oneDayMs))
                });
            }

            // 2. Calculate Quarter-Year Multiples (every 3 months)
            // Calculate total months passed roughly
            let diffMonths = (now.getFullYear() - weddingDate.getFullYear()) * 12 + (now.getMonth() - weddingDate.getMonth());
            // Check day to be precise
            if (now.getDate() < weddingDate.getDate()) diffMonths--;
            
            let startQuarter = Math.floor(diffMonths / 3) + 1;
            if (startQuarter < 1) startQuarter = 1;

            // Generate next 4 quarter milestones
            for (let i = 0; i < 4; i++) {
                const q = startQuarter + i;
                const targetDate = new Date(weddingDate);
                targetDate.setMonth(weddingDate.getMonth() + (q * 3));
                
                let years = Math.floor((q * 3) / 12);
                let remainder = (q * 3) % 12;
                
                let label = "";
                if (remainder === 0) {
                    label = `${years}. Hochzeitstag`;
                    // First year is usually "1. Jahr" or "1. Hochzeitstag"
                    if (years === 1) label = "1. Hochzeitstag";
                } else {
                    let fraction = "";
                    if (remainder === 3) fraction = "1/4";
                    else if (remainder === 6) fraction = "1/2";
                    else if (remainder === 9) fraction = "3/4";
                    
                    if (years > 0) label = `${years} ${fraction} Jahre`;
                    else label = `${fraction} Jahr`;
                }

                milestones.push({
                    label: label,
                    date: targetDate
                });
            }

            // Filter out any past dates (just in case logic was slightly off)
            const futureMilestones = milestones.filter(m => m.date > now);

            // Sort by date
            futureMilestones.sort((a, b) => a.date - b.date);

            // Take top 6
            const displayMilestones = futureMilestones.slice(0, 6);

            let milestoneHtml = '';
            displayMilestones.forEach(m => {
                const remaining = calculateRemainingTime(m.date);
                if (remaining) {
                    milestoneHtml += `
                        <tr>
                            <td>${m.label}</td>
                            <td>${formatMilestoneDate(m.date)}</td>
                            <td>${remaining.days}</td>
                        </tr>
                    `;
                }
            });
            elMilestoneList.innerHTML = milestoneHtml;
        }

        // Calculate Countdown to Next Anniversary (Footer)
        let currentYear = now.getFullYear();
        let nextAnniversary = new Date(currentYear, 8, 6, 11, 2, 0); // Month 8 is September
        
        if (now > nextAnniversary) {
            nextAnniversary = new Date(currentYear + 1, 8, 6, 11, 2, 0);
        }

        const diffNext = nextAnniversary - now;
        const oneDay = 1000 * 60 * 60 * 24;
        const oneHour = 1000 * 60 * 60;
        
        const nextDays = Math.floor(diffNext / oneDay);
        const nextHours = Math.floor((diffNext % oneDay) / oneHour);

        const nextAnniversaryWeekday = nextAnniversary.toLocaleDateString('de-DE', { weekday: 'short' });
        const nextAnniversaryDateFormatted = nextAnniversary.toLocaleDateString('de-DE', {
            day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        if (elNextAnniversary) {
            elNextAnniversary.innerText = `Noch ${nextDays} Tage und ${nextHours} Stunden bis zum nächsten Hochzeitstag (${nextAnniversaryWeekday}, ${nextAnniversaryDateFormatted} Uhr)`;
        }
    }

    // Initialize components
    displayRandomQuote();
    
    // Run timer immediately then every second
    updateTimer();
    setInterval(updateTimer, 1000);

    // Smooth scroll to anchor if hash is present in URL, after content is rendered
    window.addEventListener('load', () => {
        if (window.location.hash) {
            setTimeout(() => {
                const element = document.querySelector(window.location.hash);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100); // Give a small delay for content to fully settle
        }
    });
});
