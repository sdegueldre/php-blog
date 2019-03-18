INSERT INTO "users" ("username", "email", "password_hash", "permissions")
VALUES
  ('Julien',	'julien@julien.julien',	'$2y$12$wsNYi8h27wj4obGdghAjXuldblI.k18VUENsdvTgf/VKo08xmPCXG',	2),
  ('Samuel', 'samuel@samuel.samuel', '$2y$12$7Eonv.k29rNW.CrHupdA.eTFVzBM4yrx9tbUmnPG9OVRLk049SU46', 2),
  ('Michael', 'michael@michael.michael', '$2y$12$Zofzsrp//R369c8WkRU0gOb5fUQQA8zmX3Lj0yeVoO9G1ZQLHuXfq', 2),
  ('Simon', 'simon@simon.simon', '$2y$12$xp6qJ5iddO5AUJBYwqfiterQslBloTGEq4vCjUKlrD8SaHm5vg2wO', 2);


INSERT INTO "categories" ("name")
VALUES
  ('lifestyle'),
  ('ethical lifehacks'),
  ('blockchain frameworks');

INSERT INTO "articles" ("title", "text", "author_id")
VALUES
  (
    'article Julien 1',
    '<p><strong>Lorem ipsum </strong>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</p><blockquote><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</p></blockquote><ol><li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li><li>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li></ol>',
    (SELECT id FROM users WHERE username='Julien')
  ),
  (
    'article Julien 2',
    '<p><i>Lorem ipsum</i><strong> </strong>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</p><blockquote><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</p></blockquote><blockquote><ol><li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li><li>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li></ol></blockquote>',
  (SELECT id FROM users WHERE username='Julien')
  ),
  (
    'article Michael 1',
    '<p><strong>Lorem ipsum </strong>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</p><blockquote><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</p></blockquote><ol><li><i>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</i></li><li>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li></ol>',
    (SELECT id FROM users WHERE username='Michael')
  ),
  (
    'article Michael 2',
    '<p><strong>Lorem ipsum </strong>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</p><p><i><strong>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</strong></i></p><ul><li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li></ul><ol><li>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li></ol>',
    (SELECT id FROM users WHERE username='Michael')
  ),
  (
    'article Samuel 1',
    '<blockquote><p><strong>Lorem ipsum </strong>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</p></blockquote><blockquote><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</p></blockquote><ol><li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li><li>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li></ol>',
    (SELECT id FROM users WHERE username='Samuel')
  ),
  (
    'article Samuel 2',
    '<p><strong>Lorem ipsum </strong>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</p><p>Ut enim ad <strong>minim</strong> veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</p><ol><li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li></ol><p>&nbsp;</p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
    (SELECT id FROM users WHERE username='Samuel')
  ),
  (
    'article Simon 1',
    '<p><strong>Lorem ipsum </strong>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</p><p>Ut enim ad <strong>minim</strong> veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</p><ol><li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li></ol><p>&nbsp;</p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
    (SELECT id FROM users WHERE username='Simon')
  ),
  (
    'article Simon 2',
    '<p><strong>Lorem ipsum </strong><i>dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.&nbsp;</i></p><blockquote><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.&nbsp;</p></blockquote><ol><li><strong>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</strong></li><li>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li></ol>',
    (SELECT id FROM users WHERE username='Simon')
  );
