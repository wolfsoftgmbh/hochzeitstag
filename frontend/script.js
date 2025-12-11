document.addEventListener('DOMContentLoaded', () => {
    /**
     * CONFIGURATION
     * Set the wedding date here. 
     * Month is 0-indexed in JS Date constructor (0=Jan, 8=Sept),
     * but standard string parsing "YYYY-MM-DDTHH:mm:ss" is easier/safer. 
     */
    const WEDDING_DATE_STR = "2025-09-06T11:02:00"; 
    
    // History Events Configuration
    const historyEvents = [
        { name: "Erster Kontakt", date: "2014-01-11T19:02:00" },
        { name: "Zusammen", date: "2014-04-01T21:02:00" },
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

    // List of humorous and affectionate German quotes
    const quotes = [
        "Liebe ist, wenn man sich auch ohne Worte versteht,\naber trotzdem stundenlang quatschen kann.",
        "Wir sind wie zwei Puzzleteile:\nUnterschiedlich, aber wir passen perfekt zusammen.",
        "Ehe ist, wenn man sich streitet, wer recht hat,\nund am Ende beide lachen müssen.",
        "Zusammen sind wir einfach besser.\nWie Kaffee und Kuchen am Sonntagnachmittag.",
        "Du bist mein Lieblingsmensch,\nauch wenn du mir manchmal den letzten Keks klaust.",
        "Liebe ist das einzige,\nwas mehr wird, wenn wir es verschwenden.",
        "Mit dir wird sogar der Einkauf im Supermarkt\nzu einem kleinen Abenteuer.",
        "Wir passen zusammen wie\nTopf und Deckel (auch wenn es manchmal klappert).",
        "Echte Liebe ist,\nwenn man sich gegenseitig beim Schnarchen erträgt.",
        "Du bist mein Anker im Sturm\nund mein Konfetti im Alltag.",
        "Zuhause ist da,\nwo du bist (und wo das WLAN funktioniert).",
        "Wir sind ein perfektes Team:\nIch sorge für das Chaos, du für die Ordnung.",
        "Liebe heißt nicht, dass man sich nie streitet,\nsondern dass man sich immer wieder verträgt.",
        "Mit dir an meiner Seite\nist jeder Tag ein kleiner Feiertag.",
        "Du bist der Grund,\nwarum ich öfter auf mein Handy schaue und lächle.",
        "Unsere Liebe ist wie guter Wein:\nSie wird mit den Jahren immer besser.",
        "Danke, dass du meine Macken nicht nur erträgst,\nsondern sie sogar ein bisschen magst.",
        "Wir zwei gegen den Rest der Welt\n(und gegen den Abwasch).",
        "Du bringst mich zum Lachen,\nselbst wenn ich eigentlich grummelig sein will.",
        "Glück ist, jemanden zu haben,\nmit dem man auch mal herrlich albern sein kann.",
        "Du bist der Zucker in meinem Kaffee.",
        "Egal wohin wir gehen, Hauptsache zusammen.",
        "Mit dir macht sogar Nichtstun Spaß.",
        "Du bist mein Happy Place.",
        "Liebe ist, wenn wir uns blind verstehen.",
        "Ich mag dich ein bisschen mehr als Pizza.",
        "Du und ich – das passt einfach.",
        "Mein Herz schlägt im Takt von deinem.",
        "Deine Umarmung ist mein Lieblingsort.",
        "Zusammen ist man weniger allein.",
        "Du bist mein Lieblings-Nervzwerg.",
        "Ich liebe dich mehr als Kaffee (aber sag es dem Kaffee nicht).",
        "Wir sind wie Pech und Schwefel, nur hübscher.",
        "Du hast den Schlüssel zu meinem Herzen (und zum Kühlschrank).",
        "Liebe ist, wenn du mir das letzte Stück Schokolade überlässt.",
        "Du bist der Grund, warum ich morgens aufstehe (meistens).",
        "Mit dir ist sogar der Abwasch erträglich.",
        "Wir sind das perfekte Chaos.",
        "Du bist mein liebster Zeitvertreib.",
        "Ich liebe dich, auch wenn du hungrig bist.",
        "Du bist mein persönlicher Superheld (ohne Umhang).",
        "Zusammen sind wir unschlagbar (im Faulenzen).",
        "Du bist mein Lieblingsmensch, Punkt.",
        "Liebe ist, wenn man sich auch schweigend anschreien kann.",
        "Du bist süßer als Zuckerwatte.",
        "Ich würde mein Handy für dich weglegen.",
        "Du bist der Käse auf meiner Pizza.",
        "Wir passen zusammen wie Pommes und Ketchup.",
        "Du bist mein Einhorn in einer Herde von Pferden.",
        "Liebe ist, wenn man gemeinsam dick wird.",
        "Du bist der Grund für mein Dauergrinsen.",
        "Ich liebe dich mehr als gestern (und weniger als morgen).",
        "Du bist meine bessere Hälfte (die vernünftigere).",
        "Mit dir kann man Pferde stehlen (und Ponys).",
        "Du bist mein Lieblings-Kuscheltier.",
        "Liebe ist, wenn man sich die Decke teilt (widerwillig).",
        "Du bist der Hit in meinen Charts.",
        "Ich folge dir überall hin (außer aufs Klo).",
        "Du bist mein Highlight des Tages.",
        "Wir sind wie Bonnie und Clyde, nur ohne Banküberfall.",
        "Du bist mein 6er im Lotto.",
        "Ich liebe dich bis zur Unendlichkeit und viel weiter.",
        "Du bist mein Fels in der Brandung (und mein Kissen).",
        "Mit dir wird es nie langweilig.",
        "Du bist mein Lieblings-Abenteuer.",
        "Liebe ist, wenn man sich blind vertraut (aber trotzdem Google Maps checkt).",
        "Du bist mein Sternenhimmel.",
        "Ich hab dich zum Fressen gern.",
        "Du bist mein Lieblings-Gedanke vor dem Einschlafen.",
        "Wir sind ein Dream-Team.",
        "Du bist mein Sonnenschein, auch nachts.",
        "Ich liebe dich mehr als Schokolade (und das heißt was).",
        "Du bist mein Herzblatt.",
        "Mit dir ist das Leben ein Ponyhof.",
        "Du bist mein allerliebster Lieblingsmensch.",
        "Liebe ist, wenn man sich gegenseitig die Sätze beendet.",
        "Du bist der Grund, warum ich so glücklich bin.",
        "Ich bin süchtig nach dir.",
        "Du bist mein Zuhause.",
        "Wir sind einfach füreinander gemacht."
    ];

    /**
     * Selects a random quote and displays it.
     */
    function displayRandomQuote() {
        if (!elQuoteDisplay) return;
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
     * Calculates remaining time (Months, Days, Hours, Min) to a target date.
     */
    function calculateRemainingTime(targetDate) {
        const now = new Date();
        let diff = targetDate - now;
        
        if (diff <= 0) return null; // Date passed

        // Calculate Months
        let tempDate = new Date(now);
        let months = 0;
        while (true) {
            // Add 1 month safely
            let nextMonth = new Date(tempDate);
            nextMonth.setMonth(nextMonth.getMonth() + 1);
            
            // Handle month overflow
            if (nextMonth > targetDate) break;
            
            tempDate = nextMonth;
            months++;
        }

        // Calculate remaining diff after months
        let remainingDiff = targetDate - tempDate;
        
        const oneDay = 1000 * 60 * 60 * 24;
        const oneHour = 1000 * 60 * 60;
        const oneMinute = 1000 * 60;

        const days = Math.floor(remainingDiff / oneDay);
        const hours = Math.floor((remainingDiff % oneDay) / oneHour);
        const minutes = Math.floor((remainingDiff % oneHour) / oneMinute);

        return { months, days, hours, minutes };
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
                            <td>${remaining.months}</td>
                            <td>${remaining.days}</td>
                            <td>${remaining.hours}</td>
                            <td>${remaining.minutes}</td>
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
});
