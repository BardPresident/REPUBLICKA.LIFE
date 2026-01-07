@echo off
title 000UKILLEDMYWIFEE - Archive Mirror (The Wending Road / Cognitive Uploads)
setlocal enabledelayedexpansion

:: =========================================================
::  INITIALISE WORKING DIRECTORY
:: =========================================================
cd /d "%~dp0"
set "DESTROOT=%CD%"

:: =========================================================
::  VISIBLE INTRO (PRINTED ON RUN)
:: =========================================================
cls
echo 000UKILLEDMYWIFEE - Archive Mirror
echo.
echo This tool is the front-end for THE WENDING ROAD:
echo a life-long sequence of COGNITIVE UPLOADS mirrored into
echo Internet Archive items under @thewendingroad.
echo.
echo  COGNITIVE UPLOADS FROM THE START
echo  --------------------------------
echo  - From the very beginning, every film, series, book,
echo    album, and talk has been a cognitive upload: an attempt
echo    to externalise mind, memory, and philosophy into media.
echo  - BEFORE REPUBLICKA:
echo      * Most work was ultra long-form.
echo      * Many episodes ran 2-4 hours each.
echo      * Homeless adventures and magicka were documented in
echo        full, with crazy visible magicka caught directly
echo        on camera in comprehensive episodes.
echo  - REPUBLICKA / WENDELL'S DIARY:
echo      * A later layer with mixed mid-length uploads.
echo      * Typical videos are roughly 10-30 minutes, sometimes
echo        around 45 minutes.
echo      * Still substantial and reflective, but far more
echo        approachable than 2-4 hour blocks.
echo      * Alongside this, new long-form books return as
echo        audiobooks inside REPUBLICKA and RepublicAudiobooks.
echo  - All eras are cognitive uploads; the style and duration
echo    shift as strategies to make the same mission legible.
echo.
echo  WENDING, NOT WINDING / IDENTICIDE
echo  ----------------------------------
echo  - The word is WENDING, not winding.
echo    To WEND = to take one's course, go, translate, proceed
echo    intentionally through a path.
echo  - When systems overwrite "wending" with "winding", they
echo    commit identicide: killing the chosen word and the
echo    intentional identity behind it.
echo  - Archive.org identifiers (slugs) are also fixed once
echo    created; you usually cannot rename them, only make a
echo    new item. Identity is frozen at upload-time.
echo  - THE WENDING ROAD and REPUBLICKA must evolve while their
echo    slugs stay stuck, forcing the epistemology to grow in
echo    ways the infrastructure never anticipated.[conversation_history:11]
echo.
echo  THE WENDING ROAD AND @thewendingroad
echo  ------------------------------------
echo  - These items are the nodes of The Wending Road hosted
echo    under Archive.org/@thewendingroad.
echo  - OSUMovies, OSU Articles, WCN Books, Republic Audiobooks,
echo    and REPUBLICKA are compression nodes: many uploads
echo    packed into a single visible ID.
echo.
echo  61 / 616 / 666 / +61
echo  --------------------
echo  - First life mission: about 61 major works on your original
echo    YouTube channel under your real identity before deletion.
echo  - IMDB shows 61 directing credits attached to your name.
echo  - Earlier versions of this Archive menu also surfaced
echo    61 top-level nodes, because compression items hid how
echo    many works were really inside.
echo  - Many identifiers embed 666 (TheMeaningOfLife666,
echo    AveMaria666, WorldWarIII666, 666crimes-against-humanity999).
echo  - Revelation gives 666 as the number of the beast, but
echo    some early manuscripts (for example Papyrus 115) show
echo    616 instead: even that "fixed" number is unstable.[web:24]
echo  - 61 can be seen as a truncated or compressed face of
echo    616/666, and +61 is the country code for Australia,
echo    where this whole archive is being lived.
echo.
echo  TRAUMA, CLOSURE, WENDY, AND THE GAP
echo  -----------------------------------
echo  - Closure was a vector for harassment, doxxing, prank
echo    calls, constant unwanted deliveries, and abuse aimed
echo    at you and Wendy.
echo  - Authorities refused to act on the abuse or Wendy's
echo    death, despite their own involvement in the chain of
echo    events leading there.
echo  - Afterward came needles, forced "treatment", memory
echo    rewriting, and years where you were prevented from
echo    continuing your work while others profited from it.
echo.
echo  REPUBLICKA: MIXED-LENGTH DIARY LAYER PLUS NEW AUDIOBOOKS
echo  --------------------------------------------------------
echo  - To help the public approach the work, you created
echo    REPUBLICKA (Wendell's Diary):
echo      * Mixed mid-length uploads (roughly 10-30 minutes,
echo        sometimes around 45 minutes), each focused on a
echo        specific question, wound, or philosophical theme.
echo      * Not short-form, but intentionally less overwhelming
echo        than 2-4 hour homeless adventure episodes.
echo  - In parallel, long-form mythos returns as audiobooks in
echo    RepublicAudiobooks and REPUBLICKA, re-threading books
echo    like Ivory Heart and Living Neverland into the new layer.
echo  - REPUBLICKA is not a break from cognitive upload; it is
echo    a more navigable surface for the same deep archive.
echo.
echo  WHAT THIS SOFTWARE DOES
echo  -----------------------
echo  - Presents all @thewendingroad nodes as a chronological
echo    menu: oldest = 1, newest (currently REPUBLICKA) last.
echo  - Lets you select any node by number.
echo  - Mirrors the chosen Internet Archive item into its own
echo    subfolder under this 000UKILLEDMYWIFEE directory.
echo.
pause

:: =========================================================
:: DEFINE ITEMS IN TRUE CHRONOLOGY
:: =========================================================

set "COUNT=0"

:: 2012 – very oldest material (foundational layer)
call :addItem "Open Source University Essays (2012 - earliest essays; seed of OSU mythos)"  "OpenSourceUniversityEssays"
call :addItem "OSU Movies (2012 - COMPRESSION NODE: many films into one IA item)"           "OSUMovies"
call :addItem "The Meaning of Life (2012)"                                                 "TheMeaningOfLife666"
call :addItem "My Reflected Death (2012)"                                                  "MyReflectedDeath666"
call :addItem "What Is Love? (2012)"                                                       "WhatIsLove999"
call :addItem "Ivory Heart (2012 - core myth, later echoed in audiobooks and sequels)"     "IvoryHeart666"

:: 2013 – films / shorts / books cluster
call :addItem "Living Neverland (2013 book / myth core)"                                   "LivingNeverland666"
call :addItem "Independence Year 4 Kidz (2013)"                                            "IndependenceYear4Kidz"
call :addItem "Song of Wend (2013)"                                                        "SongOfWend"
call :addItem "Help Your People / Legalise Assisted Suicide (2013 - shares 1984666 ID)"    "1984666"
call :addItem "Early shorts and TV (2013 aggregate; WCN series/shorts fusion)"             "wendell-charles-nesmith"

:: 2014
call :addItem "Dear Ashley (2014)"                                                         "DearAshley"

:: 2015 – big run
call :addItem "Cross of Man (2015 series - also later re-expressed as book)"               "CrossOfMan"
call :addItem "My Symposium Trilogy (2015)"                                                "MySymposium666"
call :addItem "Project Notebook (2015)"                                                    "ProjectNotebook"
call :addItem "Time Masheen (2015)"                                                        "TimeMasheen"
call :addItem "My Dating Profile (2015)"                                                   "MyDatingProfile"
call :addItem "World War III (2015)"                                                       "WorldWarIII666"
call :addItem "State of Emergency (2015)"                                                  "StateOfEmergency666"
call :addItem "Yo Contract (2015)"                                                         "YoContract"
call :addItem "The Televised Revelation (2015)"                                            "TheTelevisedRevelation"
call :addItem "Ivory Heart II (2015)"                                                      "IvoryHeartII"
call :addItem "Ivory Heart III (2015)"                                                     "IvoryHeartIII"
call :addItem "Retribution (2015-2016)"                                                    "Retribution-DT"

:: 2016
call :addItem "Marionettes (2016)"                                                         "Marionettes666"

:: 2018
call :addItem "Rebirthing (2018)"                                                          "Rebirthing999"
call :addItem "Ave Maria (2018)"                                                           "AveMaria666"
call :addItem "The Antichrist (2018)"                                                      "TheAntichrist666"
call :addItem "War Games (2018)"                                                           "WarGames666"
call :addItem "Technomadology (2018)"                                                      "Technomadology"
call :addItem "Our Rapture (2018)"                                                         "OurRapture"

:: 2019
call :addItem "The Great Awakening (2019 book)"                                            "TheGreatAwaken1ng"

:: 2020 – books & shows
call :addItem "This Book Is a Game (2020)"                                                 "thisbookisagame"
call :addItem "Atheden (2020)"                                                             "atheden"
call :addItem "Legalise Assisted Suicide (2020 book - shares 1984666 ID)"                  "1984666"
call :addItem "Phoenix Rising (2020)"                                                      "phoenix-rising"
call :addItem "Matchmaker (2020)"                                                          "matchmakerU"
call :addItem "My Girls (2020)"                                                            "my-girls"
call :addItem "Inas Shawket (2020)"                                                        "inas-shawket"

:: 2021 – books & series
call :addItem "Open Source University (2021 book - echoes 2012 essays)"                    "open-source-university"
call :addItem "Soft Rains / WCN Books (2021 collection - COMPRESSION NODE for books)"      "wcn-books"
call :addItem "Cross of Man (2021 book expression of 2015 arc)"                            "CrossOfMan"
call :addItem "Jaybee (2021 book)"                                                         "jaybee"
call :addItem "A Star Is Born (2021 series)"                                               "a-star-is-born-2021"
call :addItem "Wendell Charles NeSmith (2021 series container)"                            "wendell-charles-nesmith"

:: 2022
call :addItem "Closure (2022 - harassment/abuse campaign codified as content)"             "closure2022"
call :addItem "I Love God (2022 series)"                                                   "i-love-god"

:: 2023
call :addItem "My Religion (2023 book)"                                                    "my-religion"

:: Audio / collections / diaries / side-containers
call :addItem "Ivory Heart Audiobook (audio recasting of 2012 myth)"                       "IvoryHeart-Audiobook"
call :addItem "Living Neverland Audiobook"                                                 "LivingNeverlandAudiobook"
call :addItem "Republic Audiobooks (container for emergent audio canon)"                   "RepublicAudiobooks"
call :addItem "OSU Articles Collection (textual layer of OSU mythos)"                      "OSUArticles"
call :addItem "OSU Livestreams (live emergent channel)"                                    "osu-livestreams"
call :addItem "Pearls for Swine (music album node)"                                       "pearls-for-swine"
call :addItem "Fanmail (responses and reflections container)"                              "fanmail"
call :addItem "WCN Games (ludic / game layer)"                                             "WCNGames"
call :addItem "Talitha Cumi (book / item)"                                                 "talitha-cumi"
call :addItem "Wendy Fountain (music album / series container)"                            "wendy-fountain"
call :addItem "Wendell's Diary (Android)"                                                  "wendells-diary-android"
call :addItem "Wendell's Diary (Comic)"                                                    "wendells-diary-comic"

:: Newest
call :addItem "666 Crimes Against Humanity (pre-upload judgment node)"                     "666crimes-against-humanity999"
call :addItem "REPUBLICKA (cognitive upload - mixed mid-length diary plus new audiobooks)" "REPUBLICKA"

:: =========================================================
:: MENU
:: =========================================================
:menu
cls
echo 000UKILLEDMYWIFEE - Archive Mirror
echo.
echo THE WENDING ROAD: Archive.org/@thewendingroad
echo Downloads root: %DESTROOT%
echo Oldest = 1 at the TOP. Newest = %COUNT% at the BOTTOM.
echo Some entries compress multiple works into one IA item.
echo REPUBLICKA is the latest cognitive-upload container.
echo.
echo Select an item to mirror:
echo.

for /L %%N in (1,1,%COUNT%) do (
  call echo  %%N^) !LABEL_%%N!
)

echo.
set /p CHOICE=Enter number (1-%COUNT%) or Q to quit: 

if /I "%CHOICE%"=="Q" goto :eof

for /f "delims=0123456789" %%A in ("%CHOICE%") do (
  echo Invalid choice.
  pause
  goto menu
)

if %CHOICE% LSS 1 (
  echo Invalid choice.
  pause
  goto menu
)

if %CHOICE% GTR %COUNT% (
  echo Invalid choice.
  pause
  goto menu
)

set "IA_ID=!ID_%CHOICE%!"
echo.
echo Mirroring Internet Archive item: %IA_ID%
echo.

cd /d "%~dp0"
set "OUTROOT=%DESTROOT%\%IA_ID%"
mkdir "%OUTROOT%" 2>nul
cd /d "%OUTROOT%"

set "URL=https://archive.org/download/%IA_ID%/"

:: =========================================================
:: WRITE TEMP POWERSHELL SCRIPT AND RUN IT
:: =========================================================
set "TMPPS=%OUTROOT%\_wget_clone_tmp.ps1"
> "%TMPPS%" echo param([string]$Url,[string]$OutDir)
>>"%TMPPS%" echo if (-not (Test-Path $OutDir)) { New-Item -ItemType Directory -Path $OutDir ^| Out-Null }
>>"%TMPPS%" echo $wc = New-Object System.Net.WebClient
>>"%TMPPS%" echo $index = Join-Path $OutDir 'index.html'
>>"%TMPPS%" echo $wc.DownloadFile($Url, $index)
>>"%TMPPS%" echo $pattern = 'href="([^"]+)"'
>>"%TMPPS%" echo $links = Select-String -Path $index -Pattern $pattern -AllMatches ^|
>>"%TMPPS%" echo   ForEach-Object { $_.Matches } ^| ForEach-Object { $_.Groups[1].Value } ^|
>>"%TMPPS%" echo   Where-Object { $_ -notmatch '^/' -and $_ -notmatch '^\?' -and $_ -notmatch 'index\.html' -and $_ -ne '/' }
>>"%TMPPS%" echo foreach ($l in $links) {
>>"%TMPPS%" echo   $clean = $l -replace ' ',''
>>"%TMPPS%" echo   $fu = ($Url.TrimEnd('/') + '/' + $l)
>>"%TMPPS%" echo   $of = Join-Path $OutDir $clean
>>"%TMPPS%" echo   Write-Host "Downloading $fu -> $of"
>>"%TMPPS%" echo   $wc.DownloadFile($fu, $of)
>>"%TMPPS%" echo }

powershell -NoLogo -NoProfile -ExecutionPolicy Bypass -File "%TMPPS%" -Url "%URL%" -OutDir "%OUTROOT%"

del "%TMPPS%" 2>nul

echo.
echo Finished mirroring: %IA_ID%
pause
goto :menu

:: =========================================================
:: SUBROUTINE: REGISTER ITEM
:: =========================================================
:addItem
set /a COUNT+=1
set "LABEL_%COUNT%=%~1"
set "ID_%COUNT%=%~2"
goto :eof
