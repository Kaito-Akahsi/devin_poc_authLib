<?php

use AuthLib\Auth\AuthService;
use AuthLib\DataStore\DataStoreFactory;
use AuthLib\Validation\InputValidator;
use AuthLib\Auth\PasswordHasher;
use AuthLib\Auth\SaltGenerator;
use AuthLib\Config\ConfigLoader;
use AuthLib\Config\ConfigReader;

$loader = new ConfigLoader();
$config = $loader->loadFromIniFile(__DIR__.'/authlib.ini');
$reader = new ConfigReader($config);

$factory = new DataStoreFactory($reader);
$datastore = $factory->createDataStore();
$validator = new InputValidator();
$passwordHasher = new PasswordHasher(new SaltGenerator());

$authService = new AuthService($datastore, $validator, $passwordHasher, $reader);


$ret = $authService->addUser("sample@sample.com", "password");
if(!$ret->isSucceeded()){
  echo("ユーザー追加失敗");
}

$ret = $authService->login("sample@sample.com", "password");
if(!$ret->isSucceeded()){
  echo("ユーザログイン失敗");
}

$resetToken = $authService->requestPasswordReset("sample@sample.com");
if(!$resetToken->isSucceeded()){
  echo("パスリセット要求失敗");
}

$ret = $authService->resetPassword("sample@sample.com", $resetToken->getResetToken(), "newPasssword");
if(!$ret->isSucceeded()){
  echo("パスリセット失敗");
}