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

    // Funny German Poems (50 different ones)
    $poems = array(
        "Der Kater miaut, die Maus tanzt im Kreis,\nDie Liebe ist süß, so wie Erdbeereis.",
        "Im Kühlschrank, da wohnt ein kleiner Zwerg,\nder klaut die Schokolade und lacht vom Berg.",
        "Zwei Herzen, ein Gedanke, ein Pups – oh Schreck!\nSo geht die Romantik ganz schnell mal weg.",
        "Die Socken im Flur, sie streiten sich sehr,\nWer ist die schönere, wer wiegt wohl mehr?",
        "Ein Kochtopf singt, ein Besen tanzt mit Schwung,\nDie Liebe ist jung, das Leben ein Sprung.",
        "Der Mond, er gähnt, die Sterne sind müd',\nDoch unsre Liebe, die niemals verblüht.",
        "Ein Kaktus küsst, ein Frosch klettert hoch,\nDas Glück ist manchmal ein kleines Loch.",
        "Die Wolken ziehen, der Wind pfeift ein Lied,\nManchmal hab ich dich, manchmal tut's mir leid.",
        "Der Teddybär weint, der Hase lacht laut,\nWeil Liebe auch mal auf die Nase haut.",
        "Im Garten summt die Biene so froh,\nDie Liebe ist chaotisch, mal so, mal so.",
        "Ein Elefant träumt von einem Ballett,\nMit dir wird das Leben ein buntes Bett.",
        "Die Zahnbürste winkt, die Seife lacht fein,\nSchön ist es mit dir, nicht mehr allein zu sein.",
        "Der Regen prasselt, die Sonne lacht scheinbar,\nUnsere Liebe ist manchmal wie ein Gewitter, wunderbar.",
        "Ein Keks krümelt, ein Löffel fällt tief,\nMit dir wird das Leben ein lustiger Brief.",
        "Die Uhu schreit, der Frosch quakt im Teich,\nUnsere Liebe ist einzigartig, ungleich.",
        "Im Blumentopf blüht ein Gänseblümchen klein,\nMit dir ist jeder Tag ein Sonnenschein.",
        "Ein Gummiball hüpft, ein Stein rollt davon,\nUnsere Liebe ist stark, wie ein Marathon.",
        "Der Computer spinnt, das Handy macht 'piep',\nLiebe ist wie ein Code, mal tief, mal lieb.",
        "Die Wolke weint, der Regenbogen lacht,\nUnsere Liebe, die hat uns so stark gemacht.",
        "Ein Spatz zwitschert, die Katze schläft sacht,\nMit dir hab ich immer den besten Tag und Nacht.",
        "Der Kühlschrank brummt, der Ofen ist heiß,\nUnsere Liebe ist süß, wie das Paradies.",
        "Ein Buch fällt runter, ein Stift rollt weit,\nUnsere Liebe ist stark, für alle Zeit.",
        "Der Schlüssel klirrt, die Tür geht auf,\nMit dir nehm ich jeden Lebenslauf in Kauf.",
        "Die Blume welkt, die Frucht fällt vom Baum,\nUnsere Liebe ist wie ein schöner Traum.",
        "Der Fisch schwimmt schnell, der Vogel fliegt hoch,\nUnsere Liebe ist immer im Gleichschritt, doch.",
        "Ein Blatt fällt ab, ein Ast bricht entzwei,\nMit dir ist jeder Abschied nur ein 'bis bald', nicht 'vorbei'.",
        "Der Hund bellt laut, die Katze schnurrt sacht,\nUnsere Liebe hat uns so glücklich gemacht.",
        "Ein Stuhl wackelt, ein Tisch steht fest,\nUnsere Liebe ist stabil, besteht jeden Test.",
        "Die Lampe leuchtet, die Kerze brennt hell,\nMit dir vergeht die Zeit immer so schnell.",
        "Der Ball fliegt hoch, der Stein liegt still,\nUnsere Liebe erfüllt jeden Wunsch, jeden Will.",
        "Ein Tropfen fällt, ein Bach fließt sacht,\nUnsere Liebe hat uns so viel Freude gebracht.",
        "Der Wecker klingelt, der Tag beginnt neu,\nMit dir ist jeder Morgen voller Treu'.",
        "Die Gitarre klingt, das Klavier spielt fein,\nMit dir ist jeder Moment ein Gewinn, mein Schatz, mein Hein.",
        "Ein Wort gesagt, ein Blick geschenkt,\nUnsere Liebe hat uns fest aneinander gebänkt.",
        "Der Traum verfliegt, die Realität bleibt,\nUnsere Liebe, sie uns immer vorantreibt.",
        "Der Tee ist heiß, der Kaffee ist stark,\nUnsere Liebe hinterlässt stets ihren Mark.",
        "Ein Lied gesungen, ein Tanz gewagt,\nUnsere Liebe hat uns nur Gutes gesagt.",
        "Der Wind weht sanft, die Sonne lacht leis',\nUnsere Liebe ist so besonders, so preis.",
        "Ein Geheimnis geteilt, ein Lachen geschenkt,\nUnsere Liebe, sie uns ewig verbindet, gelenkt.",
        "Der Abschied fällt schwer, die Wiedersehensfreud' groß,\nUnsere Liebe ist endlos, wie ein Fluss, so los.",
        "Das Herz schlägt schnell, die Seele lacht hell,\nUnsere Liebe, sie macht uns beide so schnell.",
        "Die Zeit verrinnt, die Erinnerung bleibt,\nUnsere Liebe, sie uns stets vorantreibt.",
        "Der Anfang war süß, das Ende ist fern,\nUnsere Liebe, sie leuchtet so hell wie ein Stern.",
        "Ein Versprechen gegeben, ein Ring getauscht,\nUnsere Liebe, sie hat uns tief berauscht.",
        "Der Tag vergeht schnell, die Nacht kommt heran,\nUnsere Liebe, sie hat längst begonnen, ihr Bann.",
        "Die Hoffnung stirbt nie, der Glaube bleibt fest,\nUnsere Liebe, sie besteht jeden Test, jedes Nest.",
        "Das Glück ist ein Vogel, es fliegt manchmal fort,\nDoch unsere Liebe, sie bleibt an diesem Ort.",
        "Ein Blick genügt, ein Wort ist zu viel,\nUnsere Liebe, sie kennt unser Gefühl, unser Spiel.",
        "Der Sternenhimmel glänzt, der Mond lacht dazu,\nUnsere Liebe, sie findet immer zu dir, zu mir, zu uns, oh du."
    );

    $random_poem = $poems[ array_rand( $poems ) ];

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
                .poem { font-style: italic; color: #777777; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eeeeee; }
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
                
                <div class=\"poem\">
                    <p>Ein kleiner Gruß, der Freude bringt:</p>
                    <p>{$random_poem}</p>
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