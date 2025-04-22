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


/*
 *
 *
 Κατεβασμα κατάταξης ΠΟΦΕΑΠ
https://www.pofepa.gr/piiotanualphakappaepsilonsigmaf-alphaxiiotaomicronlambdaomicrongammaetasigmaetasigmaf.html
as
App/Data/pofepa.xlsx
[dsphinx@ace]─[/space/www/PingPong/App]$  ./importEXCEL.php

 */

require_once __DIR__. '/../../App/TTennis.php';
TTennis::showMessage("Επίσημη Κατάταξη ΒΟΡΕΙΑΣ ΕΛΛΑΔΑΣ ", "ΠΟΦΕπΑ");


// Ανάκτηση όλων των παικτών
$players = miniMVController::$db->fetchAll("SELECT * FROM POFEPA_Ranking ORDER BY score");
?>



<div class="container mt-5">
    <?php if (count($players) > 0): ?>
        <div class="table-responsive">
            <table id="playersTable" class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ΔΑΙ</th>
                    <th>Επώνυμο</th>
                    <th>Όνομα</th>
                    <th>Έτος Γέννησης</th>
                    <th>Βετεράνος</th>
                    <th>Σημειώσεις</th>
                    <th>Score</th>
                    <th>COPY</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?= htmlspecialchars($player['id']) ?></td>
                        <td><?= htmlspecialchars($player['DAI']) ?></td>
                        <td><?= htmlspecialchars($player['surname']) ?></td>
                        <td><?= htmlspecialchars($player['name']) ?></td>
                        <td><?= htmlspecialchars($player['yearDOB']) ?></td>
                        <td><?= $player['veteran'] == 1 ? 'Ναι' : 'Όχι' ?></td>
                        <td><?= htmlspecialchars($player['notes']) ?></td>
                        <td><?= htmlspecialchars($player['score']) ?></td>
                        <td>
                            <a style="text-decoration: none;" href="?page=ranking/copyAthleteLocal&id=<?=  htmlspecialchars($player['id'])?>"  target="copy">✍️</a>
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

</div>

<script>
    $(document).ready(function() {
        $('#playersTable').DataTable({
            pageLength: 25,
            language: {
                url: 'App/Javascript/el.json'
            }
        });
    });
</script>