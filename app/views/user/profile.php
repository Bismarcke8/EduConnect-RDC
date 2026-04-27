<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl ec-profile-page">
    <section class="card ec-profile-hero">
        <div class="ec-cover-gradient"></div>
        <div class="ec-profile-hero-content">
            <img src="<?php echo $user['profile_photo'] ? '/' . $user['profile_photo'] : APP_BASE_PATH . '/assets/images/default-avatar.png'; ?>"
                 alt="<?php echo htmlspecialchars($user['first_name']); ?>"
                 class="avatar avatar-lg ec-profile-avatar">
            <div>
                <h2 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <?php if (!empty($user['university'])): ?>
                    <p class="mb-1" style="color: var(--color-text-secondary);">📍 <?php echo htmlspecialchars($user['university']); ?></p>
                <?php endif; ?>
                <?php if (!empty($user['field_of_study'])): ?>
                    <p class="mb-0" style="color: var(--color-text-secondary);">🎓 <?php echo htmlspecialchars($user['field_of_study']); ?></p>
                <?php endif; ?>
            </div>
            <div class="ms-auto">
                <?php if ($isOwnProfile): ?>
                    <div class="d-flex gap-2">
                        <a href="user/invitations" class="btn btn-secondary">Invitations</a>
                        <a href="user/settings" class="btn btn-primary">Modifier le profil</a>
                    </div>
                <?php elseif ($isFollowing): ?>
                    <form method="POST" action="user/<?php echo $user['id']; ?>/unfollow">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-secondary">Abonné ✓</button>
                    </form>
                <?php elseif ($incomingInvitePending): ?>
                    <div class="d-flex gap-2">
                        <form method="POST" action="user/<?php echo $user['id']; ?>/invite/accept">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-primary">Accepter invitation</button>
                        </form>
                        <form method="POST" action="user/<?php echo $user['id']; ?>/invite/decline">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-secondary">Refuser</button>
                        </form>
                    </div>
                <?php elseif ($outgoingInvitePending): ?>
                    <button type="button" class="btn btn-secondary" disabled>Invitation envoyée</button>
                <?php else: ?>
                    <form method="POST" action="user/<?php echo $user['id']; ?>/invite">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-primary">Envoyer invitation</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="row g-3 mt-1">
        <aside class="col-12 col-lg-4">
            <div class="card">
                <h5 class="mb-3">Statistiques</h5>
                <div class="ec-profile-stats">
                    <div><strong><?php echo (int) $followersCount; ?></strong><span>Followers</span></div>
                    <div><strong><?php echo (int) $followingCount; ?></strong><span>Suivis</span></div>
                    <div><strong><?php echo (int) $postsCount; ?></strong><span>Publications</span></div>
                </div>
            </div>

            <?php if (!empty($skills)): ?>
                <div class="card">
                    <h5 class="mb-3">Compétences</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($skills as $skill): ?>
                            <span class="badge badge-primary"><?php echo htmlspecialchars($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isOwnProfile && !empty($pendingInvites)): ?>
                <div class="card">
                    <h5 class="mb-3">Invitations reçues</h5>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($pendingInvites as $invite): ?>
                            <div class="d-flex align-items-center justify-content-between ec-invite-row">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo $invite['profile_photo'] ? '/' . $invite['profile_photo'] : APP_BASE_PATH . '/assets/images/default-avatar.png'; ?>"
                                         alt="<?php echo htmlspecialchars($invite['first_name']); ?>"
                                         class="avatar avatar-sm">
                                    <div>
                                        <strong><?php echo htmlspecialchars($invite['first_name'] . ' ' . $invite['last_name']); ?></strong>
                                        <?php if (!empty($invite['university'])): ?>
                                            <div style="color: var(--color-text-secondary); font-size: 0.85rem;"><?php echo htmlspecialchars($invite['university']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="user/<?php echo (int) $invite['sender_id']; ?>/invite/accept">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Accepter</button>
                                    </form>
                                    <form method="POST" action="user/<?php echo (int) $invite['sender_id']; ?>/invite/decline">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm">Refuser</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </aside>

        <section class="col-12 col-lg-8">
            <div class="card">
                <h5 class="mb-3">À propos</h5>
                <?php if (!empty($user['bio'])): ?>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                <?php else: ?>
                    <p style="color: var(--color-text-secondary);" class="mb-0">Aucune bio renseignée pour le moment.</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h5 class="mb-3">Publications</h5>
                <p class="mb-0" style="color: var(--color-text-secondary);">Le fil personnel sera affiché ici dans la prochaine étape.</p>
            </div>
        </section>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
