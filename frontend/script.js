document.addEventListener('DOMContentLoaded', () => {
    /**
     * CONFIGURATION
     * Set the wedding date here. 
     * Month is 0-indexed in JS Date constructor (0=Jan, 8=Sept),
     * but standard string parsing "YYYY-MM-DDTHH:mm:ss" is easier/safer. 
     */
    const WEDDING_DATE_STR = "2025-09-06T11:02:00"; 
    
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
        // Replace newline characters with HTML line breaks for display
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
     * Calculates and displays special milestone dates.
     */
    function calculateMilestones() {
        if (!elMilestoneList) return;
        const weddingDate = new Date(WEDDING_DATE_STR);
        
        // Define milestones: Label and calculation function
        const milestones = [
            {
                label: "100. Tag", 
                date: new Date(weddingDate.getTime() + (100 * 24 * 60 * 60 * 1000)) 
            },
            {
                label: "200. Tag", 
                date: new Date(weddingDate.getTime() + (200 * 24 * 60 * 60 * 1000)) 
            },
            {
                label: "300. Tag", 
                date: new Date(weddingDate.getTime() + (300 * 24 * 60 * 60 * 1000)) 
            },
            {
                label: "1/4 Jahr", 
                // 3 months approx, but precisely adding months is better for "Year" milestones
                date: new Date(new Date(weddingDate).setMonth(weddingDate.getMonth() + 3)) 
            },
            {
                label: "2/4 Jahr", 
                date: new Date(new Date(weddingDate).setMonth(weddingDate.getMonth() + 6)) 
            },
            {
                label: "3/4 Jahr", 
                date: new Date(new Date(weddingDate).setMonth(weddingDate.getMonth() + 9)) 
            }
        ];

        let html = '';
        milestones.forEach(m => {
            html += `
                <div class="milestone-item">
                    <span class="milestone-label">${m.label}:</span>
                    <span class="milestone-date">${formatMilestoneDate(m.date)}</span>
                </div>
            `;
        });

        elMilestoneList.innerHTML = html;
    }

    function updateTimer() {
        const now = new Date();
        const weddingDate = new Date(WEDDING_DATE_STR);

        // Display formatted wedding date (Start date)
        if (elWeddingDateDisplay) {
            const weddingDateFormatted = weddingDate.toLocaleDateString('de-DE', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            elWeddingDateDisplay.innerText = `Start: ${weddingDateFormatted} Uhr`;
        }

        // 1. Calculate Time Since Wedding
        // Get difference in milliseconds
        let diff = now - weddingDate;

        // If wedding is in the future relative to system time, handle gracefully (show 0)
        // Although prompt context says it's Dec 2025, so wedding is past.
        if (diff < 0) diff = 0;

        // Calculate Totals
        const totalSeconds = Math.floor(diff / 1000);
        const totalHours = Math.floor(totalSeconds / 3600);

        // Calculate Breakdown (Years, Days, Hours, Minutes, Seconds)
        // We calculate "Years" based on full calendar years to be accurate
        let tempDate = new Date(weddingDate);
        let years = 0;
        
        // Add years until we go past 'now'
        while(true) {
            tempDate.setFullYear(tempDate.getFullYear() + 1);
            if(tempDate > now) {
                tempDate.setFullYear(tempDate.getFullYear() - 1);
                break;
            }
            years++;
        }

        // Remaining difference after removing years
        const diffAfterYears = now - tempDate;
        
        // Constants for time conversion
        const oneDay = 1000 * 60 * 60 * 24;
        const oneHour = 1000 * 60 * 60;
        const oneMinute = 1000 * 60;
        const oneSecond = 1000;

        const days = Math.floor(diffAfterYears / oneDay);
        const hours = Math.floor((diffAfterYears % oneDay) / oneHour);
        const minutes = Math.floor((diffAfterYears % oneHour) / oneMinute);

        // Update DOM for "Time Since"
        if (elYears) elYears.innerText = years;
        if (elDays) elDays.innerText = days;
        if (elHours) elHours.innerText = hours;
        if (elMinutes) elMinutes.innerText = minutes;

        // Format totals with German thousands separator
        if (elTotalHours) elTotalHours.innerText = totalHours.toLocaleString('de-DE');
        if (elTotalSeconds) elTotalSeconds.innerText = totalSeconds.toLocaleString('de-DE');

        // 2. Calculate Countdown to Next Anniversary
        let currentYear = now.getFullYear();
        let nextAnniversary = new Date(currentYear, 8, 6, 11, 2, 0); // Month 8 is September
        
        // If the anniversary for this year has passed, look to next year
        if (now > nextAnniversary) {
            nextAnniversary = new Date(currentYear + 1, 8, 6, 11, 2, 0);
        }

        const diffNext = nextAnniversary - now;
        
        // Calculate days and hours remaining
        const nextDays = Math.floor(diffNext / oneDay);
        const nextHours = Math.floor((diffNext % oneDay) / oneHour);

        // Format the next anniversary date for display
        const nextAnniversaryWeekday = nextAnniversary.toLocaleDateString('de-DE', { weekday: 'short' });
        const nextAnniversaryDateFormatted = nextAnniversary.toLocaleDateString('de-DE', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        if (elNextAnniversary) {
            elNextAnniversary.innerText = `Noch ${nextDays} Tage und ${nextHours} Stunden bis zum nächsten Hochzeitstag (${nextAnniversaryWeekday}, ${nextAnniversaryDateFormatted} Uhr)`;
        }
    }

    // Initialize components
    displayRandomQuote();
    calculateMilestones();

    // Run timer immediately then every second
    updateTimer();
    setInterval(updateTimer, 1000);
});
