<div class="container">
    <div id="register-container">
        <form action="/index.php?c=user&a=register" method="post">
            <div>
                <img id="background-login" src="/assets/img/background/background-login2.png" alt="baby">
            </div>

            <div>
                <label for="mail">Adresse mail</label>
                <input type="email" id="mail" name="mail" minlength="5" maxlength="150" required>
            </div>

            <div>
                <div>
                    <label for="firstname">Prénom: </label>
                    <input type="text" id="firstname" name="firstname" minlength="2" maxlength="150" required>
                </div>

                <div>
                    <label for="lastname">Nom: </label>
                    <input type="text" id="lastname" name="lastname" minlength="2" maxlength="150" required>
                </div>
            </div>

            <div>
                <div>
                    <label for="password">Password: </label>
                    <input type="password" id="password" name="password" minlength="7" maxlength="70" required>
                </div>

                <div>
                    <label for="password-repeat">Répétez le mot de passe: </label>
                    <input type="password" id="password-repeat" name="password-repeat" required>
                </div>
            </div>

           <div>
               <div>
                   <label for="age">Age</label>
                   <input type="number" id="age" name="age" required>
               </div>
           </div>



            <input class="submit-button" type="submit" name="submit" id="submit" value="S'inscrire">

        </form>
    </div>
</div>
