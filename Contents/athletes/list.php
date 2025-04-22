<?php
/**
 *  Copyright (c) 2025, dsphinx@plug.gr
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 *   1. Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *   2. Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *   3. All advertising materials mentioning features or use of this software
 *      must display the following acknowledgement:
 *      This product includes software developed by the dsphinx@plug.gr.
 *   4. Neither the name of the dsphinx nor the
 *      names of its contributors may be used to endorse or promote products
 *     derived from this software without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY dsphinx ''AS IS'' AND ANY
 *  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL dsphinx BE LIABLE FOR ANY
 *  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *  Created
 *    list.php :  12/4/25  22:02   -   dsphinx
 *
 */


require_once __DIR__. '/../../App/TTennis.php';
TTennis::showMessage("Συμμετέχοντες Αθλητές ",  TTennis::$gameTitle);

// Ανάκτηση όλων των παικτών
$players = miniMVController::$db->fetchAll("SELECT * FROM athletes ORDER BY surname, name");
?>



<div class="container mt-5">
    <?php if (count($players) > 0): ?>
        <div class="table-responsive">
            <table id="playersTable" class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                <tr>
<!--                    <th>ID</th>-->
                    <th>ΔΑΙ</th>
                    <th>Επώνυμο</th>
                    <th>Όνομα</th>
                    <th>Έτος Γέννησης</th>
                    <th>Email</th>
                    <th>Βετεράνος</th>
                    <th>Σημειώσεις</th>
                    <th> 🔧</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
<!--                        <td>--><?php //= htmlspecialchars($player['id']) ?><!--</td>-->
                        <td><?= ($player['arithmoisdeltiou']) ?></td>
                        <td><?= ($player['surname']) ?></td>
                        <td><?= ($player['name']) ?></td>
                        <td><?= ($player['yearDOB']) ?></td>
                        <td><?= ($player['email']) ?></td>
                        <td><?= $player['veteran'] == 1 ? 'Ναι' : 'Όχι' ?></td>
                        <td><?= ($player['notes']) ?></td>
                        <td>
                            <a style="text-decoration: none;" href="?page=athletes/edit&id=<?=  ($player['id'])?>"  target="_blank">🔧</a>
<!--                            <a style="text-decoration: none;" href="?page=athletes/deleteA&id=--><?php //=  htmlspecialchars($player['id'])?><!--"  target="_blank">X</a>-->
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Δεν υπάρχουν εγγραφές στη βάση.</p>
    <?php endif; ?>

    <a href="?page=athletes/insert" class="btn btn-primary mt-3">➕ Προσθήκη Νέου Παίκτη</a>
</div>

<script>
    $(document).ready(function() {
        $('#playersTable').DataTable({
            pageLength: 30,
            language: {
                url: 'App/Javascript/el.json'
            }
        });
    });
</script>