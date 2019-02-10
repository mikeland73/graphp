<?php

class GPLibrary extends GPController {

  public function isAllowed(string $method): bool {
    if (GP::isCLI()) {
      return true;
    }
    return false;
  }
}
