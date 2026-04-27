<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 920px;">
    <div class="d-flex align-items-center justify-content-between mb-lg">
        <h2>Invitations</h2>
        <a href="profile/<?php echo (int) ($_SESSION['user_id'] ?? 0); ?>" class="btn btn-secondary">Retour au profil</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <h4 class="mb-3">Recues</h4>
                <?php if (empty($incomingInvites)): ?>
                    <p style="color: var(--color-text-secondary);" class="mb-0">Aucune invitation en attente.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($incomingInvites as $invite): ?>
                            <div class="ec-invite-row js-invite-row">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo $invite['profile_photo'] ? '/' . $invite['profile_photo'] : APP_BASE_PATH . '/assets/images/default-avatar.png'; ?>"
                                             alt="<?php echo htmlspecialchars($invite['first_name']); ?>"
                                             class="avatar avatar-sm">
                                        <div>
                                            <strong><?php echo htmlspecialchars($invite['first_name'] . ' ' . $invite['last_name']); ?></strong>
                                            <div style="font-size: 0.85rem; color: var(--color-text-secondary);"><?php echo htmlspecialchars($invite['university'] ?? 'Etudiant'); ?></div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-sm js-invite-action" data-url="user/<?php echo (int) $invite['sender_id']; ?>/invite/accept" data-csrf="<?php echo htmlspecialchars($csrf_token ?? ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">Accepter</button>
                                        <button class="btn btn-secondary btn-sm js-invite-action" data-url="user/<?php echo (int) $invite['sender_id']; ?>/invite/decline" data-csrf="<?php echo htmlspecialchars($csrf_token ?? ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">Refuser</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <h4 class="mb-3">Envoyees</h4>
                <?php if (empty($outgoingInvites)): ?>
                    <p style="color: var(--color-text-secondary);" class="mb-0">Aucune invitation envoyée en attente.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($outgoingInvites as $invite): ?>
                            <div class="ec-invite-row">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo $invite['profile_photo'] ? '/' . $invite['profile_photo'] : APP_BASE_PATH . '/assets/images/default-avatar.png'; ?>"
                                         alt="<?php echo htmlspecialchars($invite['first_name']); ?>"
                                         class="avatar avatar-sm">
                                    <div>
                                        <strong><?php echo htmlspecialchars($invite['first_name'] . ' ' . $invite['last_name']); ?></strong>
                                        <div style="font-size: 0.85rem; color: var(--color-text-secondary);">En attente</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-lg">
        <div class="col-12">
            <div class="card">
                <h4 class="mb-3">Tous les étudiants</h4>
                <?php if (empty($allUsers)): ?>
                    <p style="color: var(--color-text-secondary);" class="mb-0">Aucun autre étudiant trouvé.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($allUsers as $user): ?>
                            <?php
                                $userId = (int) $user['id'];
                                $status = $inviteStatus[$userId] ?? null;
                                $isFriend = false;
                                if (!empty($friends)) {
                                    foreach ($friends as $friend) {
                                        if ($friend['id'] === $userId) {
                                            $isFriend = true;
                                            break;
                                        }
                                    }
                                }
                            ?>
                            <div class="ec-invite-row">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo $user['profile_photo'] ? '/' . $user['profile_photo'] : APP_BASE_PATH . '/assets/images/default-avatar.png'; ?>"
                                             alt="<?php echo htmlspecialchars($user['first_name']); ?>"
                                             class="avatar avatar-sm">
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                            <div style="font-size: 0.85rem; color: var(--color-text-secondary);"><?php echo htmlspecialchars($user['university'] ?? 'Etudiant'); ?></div>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if ($isFriend): ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Déjà ami</button>
                                        <?php elseif ($status && $status['direction'] === 'outgoing' && $status['status'] === 'pending'): ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Invitation envoyée</button>
                                        <?php elseif ($status && $status['direction'] === 'incoming' && $status['status'] === 'pending'): ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Vous a invité</button>
                                        <?php else: ?>
                                            <button class="btn btn-primary btn-sm js-invite-action" data-url="user/<?php echo $userId; ?>/invite" data-csrf="<?php echo htmlspecialchars($csrf_token ?? ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">Ajouter</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.js-invite-action').forEach((btn) => {
    btn.addEventListener('click', async function () {
        const url = this.dataset.url;
        const csrf = this.dataset.csrf || '';
        const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '';
        const row = this.closest('.js-invite-row');

        try {
            const response = await fetch(basePath + '/' + url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'csrf_token=' + encodeURIComponent(csrf)
            });

            const data = await response.json();
            if (data.success) {
                if (row) row.remove();
            } else {
                alert(data.error || 'Action impossible');
            }
        } catch (e) {
            alert('Erreur réseau');
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
