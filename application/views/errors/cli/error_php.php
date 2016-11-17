<?php

/*
mPDF recognises IETF language tags as:
- a single primary language subtag composed of a two letter language code from ISO 639-1 (2002), or a three letter code from ISO 639-2 (1998), ISO 639-3 (2007) or ISO 639-5 (2008) (usually written in lower case);
- an optional script subtag, composed of a four letter script code from ISO 15924 (usually written in title case);
- an optional region subtag composed of a two letter country code from ISO 3166-1 alpha-2 (usually written in upper case), or a three digit code from UN M.49 for geographical regions;
Subtags are not case sensitive, but the specification recommends using the same case as in the Language Subtag Registry, w