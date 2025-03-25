# moodle-local_placeholders

[![Moodle Plugin CI](https://github.com/sharpchi/moodle-local_placeholders/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/sharpchi/moodle-local_placeholders/actions/workflows/moodle-ci.yml)

Placeholders depends on the Shortcodes filter plugin to create shortcodes to bring live data into course content.

The following placeholders are available:

| Shortcode | Description | Arguments | Usage |
| ----------| --------- | -------- | ---- |
| modulecode | Prints the course idnumber, if set | None | [modulecode] |
| modulename | Prints the course fullname | None | [modulename] |
| level      | Prints the course customfield "level" if it exists | None | [modulelevel] |
| coordinators | Prints a contact card for anyone with the "coordinator" role in the course | Optional "title" | [coordinators title="Module leaders"] |
| librarians | Prints a contact card for anyone with the "sl" role in the course | Optional "title" | [librarians title="Subject Librarians"] |
| lecturers | Prints a contact card for anyone with the "lecturer" role in the course | Optional "title" | [lecturers title="Lecturers"] |
| contactcard | Prints a contact card for the specified role | "Role" required. Optional "title" and "exclude" | [contactcard role="unitleader" title="Module leaders" exclude="teamchat,linkedin"] |
| timetable | Not used. | | |
| startenddates | Prints a course's start and end dates with optional edit link | Optional "courseid". Will be detected if not used. | [startenddates courseid="34"] |
| coursefield | Prints any given public custom course field. If the value looks like a link, it will be made into a link | Required "name" the shortname of the field | [coursefield name="academicyear"] |
| snippet | Prints any content stored as a snippet in placeholders | Required one of "id" or "slug". Both can be present, slug is a more human readable | [snippet id="1" slug="library-information"] |
