<?php
$pageTitle = 'FAQ';
include 'header.php';

$faqFile = __DIR__ . DIRECTORY_SEPARATOR . 'faq.json';
$faqs = [];

if (is_file($faqFile)) {
    $decoded = json_decode((string) file_get_contents($faqFile), true);
    if (is_array($decoded)) {
        $faqs = array_values($decoded);
    }
}
?>

<div class="user-layout">

    <main class="faq-page">

        <h2>Frequently Asked Questions</h2>

        <p>Find answers to your questions about StudentHub.</p>

        <div class="faq-controls">

            <input
                type="text"
                id="faqSearch"
                placeholder="Search questions..."
            >

            <select id="faqCategory">
                <option value="All">All Categories</option>
                <option value="General">General</option>
                <option value="Academic">Academic</option>
                <option value="Account">Account</option>
            </select>

            <select id="faqSort">
                <option value="newest">Newest first</option>
                <option value="az">Question A-Z</option>
                <option value="za">Question Z-A</option>
            </select>

        </div>

        <p id="faqMessage">Loading FAQs...</p>

        <section class="faq-list" id="faqList"></section>

        <div class="faq-pagination">

            <button type="button" id="faqPrev" disabled>
                Previous
            </button>

            <span id="faqPageInfo">Page 0 of 0</span>

            <button type="button" id="faqNext" disabled>
                Next
            </button>

        </div>

    </main>

</div>

<script>
window.STUDENTHUB_FAQS = <?= json_encode(
    $faqs,
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
) ?>;
</script>
<script src="faq.js?v=<?= (int) (@filemtime($faqFile) ?: time()) ?>"></script>

</body>
</html>
