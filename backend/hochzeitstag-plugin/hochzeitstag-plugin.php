<?php
/**
 * Plugin Name: Hochzeitstag Countdown
 * Description: A romantic countdown to your wedding anniversary. Use shortcode [hochzeitstag] to display.
 * Version: 1.0
 * Author: Gemini
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue scripts and styles.
 */
function hochzeitstag_enqueue_assets() {
    // Only load assets if the shortcode is present (optional optimization, but good practice)
    // For simplicity, we load globally or check for post content. 
    // Here we load globally to ensure it works, but in production, conditional loading is better.
    
    // Enqueue Local Fonts
    wp_enqueue_style( 'hochzeitstag-fonts', plugins_url( 'assets/fonts/fonts.css', __FILE__ ), array(), '1.0' );

    // Enqueue Main Styles
    wp_enqueue_style( 'hochzeitstag-style', plugins_url( 'assets/style.css', __FILE__ ), array(), '1.0' );

    // Enqueue Script
    wp_enqueue_script( 'hochzeitstag-config', plugins_url( 'assets/config.js', __FILE__ ), array(), '1.0', true );
    wp_enqueue_script( 'hochzeitstag-script', plugins_url( 'assets/script.js', __FILE__ ), array('hochzeitstag-config'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'hochzeitstag_enqueue_assets' );

/**
 * Shortcode callback to render the countdown.
 */
function hochzeitstag_render_shortcode() {
    ob_start();
    ?>
    <!-- Wrapper to potentially isolate styles if needed, though scoped CSS is hard without Shadow DOM. 
         The styles in style.css are quite specific (.card, .container) but might conflict with themes. 
         For now, we use the provided structure. -->
    <div class="hochzeitstag-plugin-container">
        <div class="container">
            <div class="card">
                <div class="content-wrapper">
                    <!-- Header -->
                    <h1>Unser Hochzeitstag</h1>
                    <div class="subtitle">Zeit seit dem schönsten Tag unseres Lebens!</div>

                    <!-- Quote Block -->
                    <div id="quote-display" class="quote-box anker-zitate">
                        <!-- Quote will be inserted here by JavaScript -->
                    </div>

                    <!-- Row 1: Years, Days, Hours, Minutes -->
                    <div class="row-grid">
                        <div class="box">
                            <span class="number" id="val-years">0</span>
                            <span class="label">Jahre</span>
                        </div>
                        <div class="box">
                            <span class="number" id="val-days">0</span>
                            <span class="label">Tage</span>
                        </div>
                        <div class="box">
                            <span class="number" id="val-hours">0</span>
                            <span class="label">Stunden</span>
                        </div>
                        <div class="box">
                            <span class="number" id="val-minutes">0</span>
                            <span class="label">Minuten</span>
                        </div>
                    </div>



                    <!-- Row 3: Total Stats -->
                    <div class="stats-row">
                        <div class="box stats-box">
                            <span class="number" id="total-hours">0</span>
                            <span class="label">Gesamte Stunden</span>
                        </div>
                        <div class="box stats-box">
                            <span class="number" id="total-seconds">0</span>
                            <span class="label">Gesamte Sekunden</span>
                        </div>
                    </div>

                    <!-- Milestone Box -->
                    <div class="milestone-box">
                        <div class="milestone-title">Besondere Tage</div>
                        <div class="history-table-container">
                            <table class="history-table" id="milestone-table">
                                <thead>
                                    <tr>
                                        <th>Ereignis</th>
                                        <th>Datum</th>
                                        <th>Tage</th>
                                    </tr>
                                </thead>
                                <tbody id="milestone-list">
                                    <!-- Milestones will be inserted here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- History Box -->
                    <div class="milestone-box">
                        <div class="milestone-title">Unsere Geschichte</div>
                        <div class="history-table-container">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Ereignis</th>
                                        <th>Jahre</th>
                                        <th>Tage</th>
                                        <th>Std</th>
                                        <th>Min</th>
                                        <th>Sek</th>
                                    </tr>
                                </thead>
                                <tbody id="history-list">
                                    <!-- History items will be inserted here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer-info">
                        <span class="start-date" id="wedding-date-display"></span>
                        <div class="countdown-pill" id="next-anniversary">
                            Berechne...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'hochzeitstag', 'hochzeitstag_render_shortcode' );

/**
 * Temporary shortcode to send a test email.
 * Remove this once email functionality is confirmed.
 *
 * @param array $atts Shortcode attributes.
 * @return string Message indicating email status.
 */
function hochzeitstag_send_test_email_shortcode( $atts ) {
    if ( ! function_exists( 'wp_mail' ) ) {
        return 'WordPress Mail-Funktion (wp_mail) nicht verfügbar.';
    }

    $atts = shortcode_atts(
        array(
            'to'            => 'klaus@wolfsoft.de',
            'event_label'   => 'Nächstes Ereignis',
            'event_date'    => date( 'd.m.Y H:i', strtotime( '+7 days' ) ),
            'recipient_name'=> 'Liebe/r', // Default generic greeting
        ),
        $atts,
        'hochzeitstag_test_email'
    );

    $to_email    = sanitize_email( $atts['to'] );
    $event_label = sanitize_text_field( $atts['event_label'] );
    $event_date  = sanitize_text_field( $atts['event_date'] );
    $recipient_name = sanitize_text_field( $atts['recipient_name'] );

    $greeting = empty($recipient_name) ? 'Hallo!' : "Hallo {$recipient_name}!";

    // List of humorous and affectionate German quotes/poems (total 120, from config.js)
    $quotes = array(
        "Liebe ist, wenn man sich auch ohne Worte versteht,\naber trotzdem stundenlang quatschen kann.",
        "Wir sind wie zwei Puzzleteile:\nUnterschiedlich, aber wir passen perfekt zusammen.",
        "Ehe ist, wenn man sich streitet, wer recht hat,\nund am Ende beide lachen müssen.",
        "Zusammen sind wir einfach besser.\\nWie Kaffee und Kuchen am Sonntagnachmittag.",
        "Du bist mein Lieblingsmensch,\\nauch wenn du mir manchmal den letzten Keks klaust.",
        "Liebe ist das einzige,\nwas mehr wird, wenn wir es verschwenden.",
        "Mit dir wird sogar der Einkauf im Supermarkt\\nzu einem kleinen Abenteuer.",
        "Wir passen zusammen wie\\nTopf und Deckel (auch wenn es manchmal klappert).",
        "Echte Liebe ist,\\nwenn man sich gegenseitig beim Schnarchen erträgt.",
        "Du bist mein Anker im Sturm\\nund mein Konfetti im Alltag.",
        "Zuhause ist da,\\nwo du bist (und wo das WLAN funktioniert).",
        "Wir sind ein perfektes Team:\\nIch sorge für das Chaos, du für die Ordnung.",
        "Liebe heißt nicht, dass man sich nie streitet,\\nsondern dass man sich immer wieder verträgt.",
        "Mit dir an meiner Seite\\nist jeder Tag ein kleiner Feiertag.",
        "Du bist der Grund,\\nwarum ich öfter auf mein Handy schaue und lächle.",
        "Unsere Liebe ist wie guter Wein:\\nSie wird mit den Jahren immer besser.",
        "Danke, dass du meine Macken nicht nur erträgst,\\nsondern sie sogar ein bisschen magst.",
        "Wir zwei gegen den Rest der Welt\\n(und gegen den Abwasch).",
        "Du bringst mich zum Lachen,\\nselbst wenn ich eigentlich grummelig sein will.",
        "Glück ist, jemanden zu haben,\\nmit dem man auch mal herrlich albern sein kann.",
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
        "Wir sind einfach füreinander gemacht.",
        "Zwei Seelen im gleichen Boot, mal laut, mal leise, immer auf dem richtigen Kurs, egal wie die Reise.",
        "Liebe ist, wenn man auch nach Jahren noch schmunzelt, wenn der andere schnarcht – oder heimlich furzt.",
        "Manchmal ist das größte Abenteuer, den Fernseher zu teilen, ohne sich zu streiten, welche Serie besser ist, die alten oder die neuen Seiten.",
        "Ein Ehegelübde ist wie WLAN: Man hofft, dass es überall reicht und die Verbindung nicht abbricht, egal, wie weit man streicht.",
        "Gemeinsam alt werden? Gerne! Aber wer holt dann die Brille, wenn die andere vom Schrank fällt, und wer flucht lauter, wenn man sich verstellt?",
        "Liebe ist, wenn man den anderen so akzeptiert, wie er ist, auch wenn er seine Socken unter dem Bett vermisst.",
        "Ein Kuss am Morgen vertreibt alle Kummer und Sorgen, außer, man hat noch nicht Kaffee getrunken, dann muss man es verschieben auf morgen.",
        "Partnerschaft heißt: Man teilt Freud und Leid, und die letzte Pizza – meistens mit Streit.",
        "Das Geheimnis einer langen Beziehung? Genug Humor, um über die eigenen Fehler zu lachen, und genug Liebe, um den anderen glücklich zu machen.",
        "Wir sind wie zwei Legosteine: Perfekt zusammengefügt, aber wehe, man tritt nachts auf den anderen, dann ist der Hausfrieden verrückt!",
        "Liebe ist, wenn man sich gegenseitig die Macken vorhält und trotzdem die beste Zeit hat, die man sich wünscht und erhellt.",
        "Manchmal braucht man nur eine Umarmung, ein gutes Wort und die Gewissheit, dass man den besten Partner der Welt hat, an jedem Ort.",
        "Zwei Köpfe, ein Gedanke, ein Herzschlag im Takt, unsere Liebe ist einfach magisch, und das ist ein Fakt.",
        "Ein Leben ohne dich? Undenkbar! Wie ein Kühlschrank ohne Bier, oder ein Sonntagmorgen ohne Brötchen hier.",
        "Liebe ist: zusammen schweigen können und trotzdem alles wissen, wie die Sterne am Himmel, die man nicht will missen.",
        "Manchmal ist es ein Wunder, dass wir uns nicht schon längst erwürgt haben, bei all dem Chaos und den Launen, die wir uns tragen.",
        "Du bist mein Anker im Sturm, mein Sonnenschein im Regen, mein bester Freund und Liebhaber, einfach ein Segen.",
        "Zwei Herzen schlagen im Gleichklang, eine Melodie erklingt, unsere Liebe ist das schönste Lied, das man singt.",
        "Das Leben ist eine Reise, mal holprig, mal sanft, aber mit dir an meiner Seite, da ist es ganz charmant.",
        "Liebe ist, wenn man auch nach Jahren noch Schmetterlinge im Bauch hat, besonders, wenn der andere heimlich Schokolade hat.",
        "Ein Lächeln von dir vertreibt alle Wolken, ein Blick von dir lässt mein Herz höher schlagen, wie die Glocken.",
        "Wir sind wie Yin und Yang, Gegensätze ziehen sich an, und zusammen sind wir unschlagbar, Hand in Hand.",
        "Das Glück ist kein Zufall, es ist eine Entscheidung, und ich habe mich für dich entschieden, ohne jede Scheidung.",
        "Liebe ist, wenn man auch nach dem größten Streit noch weiß, dass man zusammengehört, wie der warme Wind und das sanfte Meereswort.",
        "Du bist mein sicherer Hafen, mein Leuchtturm im Nebel, mein Zuhause, mein Glück, mein Seelen-Rebel.",
        "Zwei Herzen, eine Flamme, ein ewiges Licht, unsere Liebe ist unendlich, sie erlischt nicht.",
        "Das Leben ist bunt mit dir an meiner Seite, wie ein Regenbogen nach dem Regen, so weit.",
        "Liebe ist, wenn man dem anderen auch mal die letzte Praline gönnt, ohne zu murren, auch wenn man sie selbst brennt.",
        "Du bist mein bester Freund, mein Vertrauter, mein Held, mit dir ist die Welt einfach viel schöner und erhellt.",
        "Ein Leben lang mit dir, das ist mein größter Wunsch, wie ein Sommerregen, der erfrischt und kein Punsch.",
        "Liebe ist, wenn man auch mal über sich selbst lacht, und der andere lacht mit, das ist die wahre Macht.",
        "Du bist mein Sonnenschein, mein Mondlicht, mein Stern, mit dir ist jeder Tag ein Gewinn, mein lieber Herrn.",
        "Zwei Menschen, ein Schicksal, ein gemeinsamer Weg, unsere Liebe ist ein Versprechen, ein ewiger Steg.",
        "Das Glück ist leise, die Liebe ist laut, mit dir hab ich alles, was mein Herz ergraut.",
        "Liebe ist, wenn man dem anderen auch mal die letzte Scheibe Brot lässt, auch wenn man selbst Hunger hat, dann ist der Test bestanden, nicht bloß ein Prost.",
        "Du bist mein Kompass im Leben, mein Wegweiser im Dunkeln, mein Licht, meine Liebe, mein strahlendes Funkeln.",
        "Ein Leben lang Hand in Hand, das ist unser Traum, unsere Liebe ist ein unendlicher Raum.",
        "Liebe ist, wenn man auch mal ohne Worte versteht, was der andere denkt, und die Zeit mit dir einfach so schnell verschenkt.",
        "Du bist mein Fels in der Brandung, mein Anker im Meer, meine Liebe zu dir wird immer mehr.",
        "Zwei Herzen, ein Beat, eine ewige Melodie, unsere Liebe, sie hört niemals auf, früh bis spät, nie.",
        "Das Leben ist schön, aber mit dir ist es schöner, wie ein Sommertag, der niemals trübe, sondern klarer.",
        "Liebe ist, wenn man auch nach Jahren noch kribbeln spürt, wenn der andere in der Nähe ist, und das Herz berührt.",
        "Du bist mein Ein und Alles, mein größter Schatz, meine Liebe zu dir ist unendlich, ohne jeden Platz.",
        "Ein Kuss von dir ist wie ein Sonnenstrahl, der wärmt, unsere Liebe, sie hat uns für immer gelehrt.",
        "Das Glück ist da, wo du bist, mein lieber Schatz, mit dir ist jeder Ort der schönste Platz.",
        "Liebe ist, wenn man auch mal über sich selbst lacht, und der andere lacht mit, das ist die wahre Macht.",
        "Du bist mein Sonnenschein, mein Mondlicht, mein Stern, mit dir ist jeder Tag ein Gewinn, mein lieber Herrn.",
        "Zwei Menschen, ein Schicksal, ein gemeinsamer Weg, unsere Liebe ist ein Versprechen, ein ewiger Steg.",
        "Das Glück ist leise, die Liebe ist laut, mit dir hab ich alles, was mein Herz ergraut.",
        "Liebe ist, wenn man dem anderen auch mal die letzte Scheibe Brot lässt, auch wenn man selbst Hunger hat, dann ist der Test bestanden, nicht bloß ein Prost.",
        "Du bist mein Kompass im Leben, mein Wegweiser im Dunkeln, mein Licht, meine Liebe, mein strahlendes Funkeln.",
        "Ein Leben lang Hand in Hand, das ist unser Traum, unsere Liebe ist ein unendlicher Raum.",
        "Liebe ist, wenn man auch mal ohne Worte versteht, was der andere denkt, und die Zeit mit dir einfach so schnell verschenkt.",
        "Du bist mein Fels in der Brandung, mein Anker im Meer, meine Liebe zu dir wird immer mehr.",
        "Zwei Herzen, ein Beat, eine ewige Melodie, unsere Liebe, sie hört niemals auf, früh bis spät, nie.",
        "Das Leben ist schön, aber mit dir ist es schöner, wie ein Sommertag, der niemals trübe, sondern klarer.",
        "Liebe ist, wenn man auch nach Jahren noch kribbeln spürt, wenn der andere in der Nähe ist, und das Herz berührt.",
        "Du bist mein Ein und Alles, mein größter Schatz, meine Liebe zu dir ist unendlich, ohne jeden Platz.",
        "Ein Kuss von dir ist wie ein Sonnenstrahl, der wärmt, unsere Liebe, sie hat uns für immer gelehrt.",
        "Das Glück ist da, wo du bist, mein lieber Schatz, mit dir ist jeder Ort der schönste Platz."
    );

    $random_quote = $quotes[ array_rand( $quotes ) ];

    $subject = 'Erinnerung: Ihr besonderes Ereignis mit Hochzeitstag Countdown';
    $message = "
        <html>
        <head>
            <title>Erinnerung an Ihr besonderes Ereignis!</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .email-container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                h2 { color: #e91e63; }
                p { color: #555555; line-height: 1.6; }
                .event-details { background-color: #fff0f5; border-left: 5px solid #e91e63; padding: 15px; margin: 20px 0; }
                .event-details p { margin: 5px 0; }
                .quote { font-style: italic; color: #777777; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eeeeee; }
                .footer { margin-top: 20px; font-size: 0.8em; color: #aaaaaa; text-align: center; }
            </style>
        </head>
        <body>
            <div class=\"email-container\">
                <h2>{$greeting}</h2>
                <p>Dies ist eine freundliche Erinnerung an Ihr bevorstehendes besonderes Ereignis:</p>
                <div class=\"event-details\">
                    <p><strong>Ereignis:</strong> {$event_label}</p>
                    <p><strong>Datum & Uhrzeit:</strong> {$event_date} Uhr</p>
                    <p>Merken Sie sich diesen wichtigen Tag vor!</p>
                </div>
                <p>Wir wünschen Ihnen viel Freude und unvergessliche Momente.</p>
                
                <div class=\"quote\">
                    <p>Ein kleiner Gruß, der Freude bringt:</p>
                    <p>{$random_quote}</p>
                </div>

                <p>Mit freundlichen Grüßen,</p>
                <p>Ihr Hochzeitstag Countdown Team</p>

                <div class=\"footer\">
                    <p>Diese E-Mail wurde vom Hochzeitstag Countdown Plugin gesendet.</p>
                </div>
            </div>
        </body>
        </html>
    ";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    $sent = wp_mail( $to_email, $subject, $message, $headers );

    if ( $sent ) {
        return "Test E-Mail wurde an <strong>{$to_email}</strong> gesendet. Bitte überprüfen Sie Ihren Posteingang (und Spam-Ordner).";
    } else {
        return "Fehler beim Senden der Test E-Mail an <strong>{$to_email}</strong>. Bitte prüfen Sie das Fehlerprotokoll Ihres Servers.";
    }
}
add_shortcode( 'hochzeitstag_test_email', 'hochzeitstag_send_test_email_shortcode' );