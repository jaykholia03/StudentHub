<?php
$pageTitle = 'FAQ';
require __DIR__ . '/bootstrap.php';

$faqFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'faq.json';
$categories = ['General', 'Academic', 'Account'];

function faq_load(string $file): array
{
    if (!is_file($file)) {
        return [];
    }

    $data = json_decode((string) file_get_contents($file), true);
    return is_array($data) ? array_values($data) : [];
}

function faq_save(string $file, array $faqs): bool
{
    $json = json_encode(array_values($faqs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents($file, $json . PHP_EOL, LOCK_EX) !== false;
}

$faqs = faq_load($faqFile);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');

    if ($action === 'delete') {
        $id = post_int('id');
        $before = count($faqs);
        $faqs = array_values(array_filter($faqs, static function ($faq) use ($id) {
            return (int) ($faq['id'] ?? 0) !== $id;
        }));

        if ($id <= 0 || count($faqs) === $before) {
            flash_set('error', 'FAQ not found.');
        } elseif (faq_save($faqFile, $faqs)) {
            flash_set('success', 'FAQ deleted.');
        } else {
            flash_set('error', 'Could not write faq.json. Check folder permissions.');
        }
    } elseif ($action === 'save') {
        $id = post_int('id');
        $question = post('question');
        $answer = post('answer');
        $category = post('category');

        if ($question === '' || $answer === '' || $category === '') {
            flash_set('error', 'Please fill in all fields.');
        } elseif (!in_array($category, $categories, true)) {
            flash_set('error', 'Please select a valid category.');
        } else {
            if ($id > 0) {
                $found = false;
                foreach ($faqs as &$faq) {
                    if ((int) ($faq['id'] ?? 0) === $id) {
                        $faq['question'] = $question;
                        $faq['answer'] = $answer;
                        $faq['category'] = $category;
                        $found = true;
                        break;
                    }
                }
                unset($faq);

                if (!$found) {
                    flash_set('error', 'FAQ not found.');
                } elseif (faq_save($faqFile, $faqs)) {
                    flash_set('success', 'FAQ updated.');
                } else {
                    flash_set('error', 'Could not write faq.json. Check folder permissions.');
                }
            } else {
                $ids = array_map('intval', array_column($faqs, 'id'));
                $faqs[] = [
                    'id' => $ids ? max($ids) + 1 : 1,
                    'question' => $question,
                    'answer' => $answer,
                    'category' => $category,
                ];

                if (faq_save($faqFile, $faqs)) {
                    flash_set('success', 'FAQ added.');
                } else {
                    flash_set('error', 'Could not write faq.json. Check folder permissions.');
                }
            }
        }
    }

    header('Location: adminfaq.php');
    exit;
}

$editId = get_int('edit');
$edit = null;
foreach ($faqs as $faq) {
    if ($editId > 0 && (int) ($faq['id'] ?? 0) === $editId) {
        $edit = $faq;
        break;
    }
}

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>
<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="dashboard-content" id="main-content">
        <div class="container-fluid">
            <h1 class="h3 font-bold mb-3">FAQ</h1>
            <p class="text-muted mb-3">Questions added here are stored in <code>faq.json</code>, not in the database.</p>
            <?php include __DIR__ . '/partials/alert.php'; ?>

            <form method="POST" class="dashboard-card mb-3">
                <div class="dashboard-card-header">
                    <h5 class="mb-0"><?php echo $edit ? 'Edit FAQ' : 'Add FAQ'; ?></h5>
                </div>
                <div class="dashboard-card-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?php echo (int) ($edit['id'] ?? 0); ?>">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Question</label>
                            <input class="form-control" name="question" required value="<?php echo e($edit['question'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo e($category); ?>" <?php echo (($edit['category'] ?? 'General') === $category) ? 'selected' : ''; ?>>
                                        <?php echo e($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Answer</label>
                            <textarea class="form-control" name="answer" rows="4" required><?php echo e($edit['answer'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit"><?php echo $edit ? 'Update FAQ' : 'Add FAQ'; ?></button>
                        <?php if ($edit): ?><a class="btn btn-light" href="adminfaq.php">Cancel</a><?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h5 class="mb-0">Existing FAQs (<?php echo count($faqs); ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Category</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$faqs): ?>
                                <tr><td colspan="4" class="text-muted">No FAQs have been added yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($faqs as $faq): ?>
                                <tr>
                                    <td><?php echo e($faq['question'] ?? ''); ?></td>
                                    <td><?php echo nl2br(e($faq['answer'] ?? '')); ?></td>
                                    <td><?php echo e($faq['category'] ?? ''); ?></td>
                                    <td class="text-end text-nowrap">
                                        <a class="btn btn-sm btn-outline-secondary" href="adminfaq.php?edit=<?php echo (int) ($faq['id'] ?? 0); ?>">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this FAQ?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) ($faq['id'] ?? 0); ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
<?php include __DIR__ . '/partials/scripts.php'; ?>
