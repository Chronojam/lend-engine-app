{
  description = "It does things and stuff!";
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/23.05";
    flake-utils.url = "github:numtide/flake-utils";

    nixpkgs-mysql57.url = "https://github.com/NixOS/nixpkgs/archive/611bf8f183e6360c2a215fa70dfd659943a9857f.tar.gz";
    nixpkgs-mysql57.flake = false;

    nixpkgs-php74.url = "https://github.com/NixOS/nixpkgs/archive/fa248afdd6ffb9de62880ea50756cf2de06c58af.tar.gz";
    nixpkgs-php74.flake = false;
  };
  outputs = {self, nixpkgs, flake-utils, nixpkgs-php74, nixpkgs-mysql57, ...}: 
    flake-utils.lib.eachDefaultSystem (system:
      let 
        pkgs = import nixpkgs { 
          inherit system; 
          config.allowUnfree = true;
        };

        phppkgs = import nixpkgs-php74 {
          inherit system;
          config.allowUnfree = true;
        };

        mysqlpkgs = import nixpkgs-mysql57 {
          inherit system;
          config.allowUnfree = true;
        };

        php = phppkgs.php74.buildEnv { extraConfig = "memory_limit = -1"; };

      in {
        devShell = (pkgs.callPackage ./shell.nix {
          # The project requires php 7.4, but the dependencies mandate composer 2.x
          php = php;
          composer = phppkgs.php74.packages.composer;
          mysql = mysqlpkgs.mysql57;
        });
      });
} 