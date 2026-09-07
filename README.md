# php-wccm
A PHP remake of WCCM

You can find the original version [here](https://github.com/werSquared/wccm).

I kept a lot of the same ideas as the original version, however, I opted to remove the symbols in favor of a convention-over-configuration approach.

Instead of using symbols like $ or =, you go based off line numbers.

Here's how my version looks:

```
drink | dɹINkH |
noun verb
plural drinks
--
meaning
meaning
meaning
--
beverage
liquid
sip
gulp
swallow
~~
food
solid
edible
snack
meal
```
Line one is the word and its pronunciation -- drink and dɹINkH.

Line two are the parts of speech -- noun or verb.

Line three is the plural form of the word -- drinks.

Line 4 opens the meanings section.

The next -- closes the meanings section.

Between this -- and the ~~ are the synonyms.

Between the ~~ and the end of the file are the antonyms.

The output is formatted in the same sense as the original version, but with a couple altercations.

Here's how it looks:

```
Drink | dɹɪŋkʰ |
noun verb
Plural: drinks

Meanings:
   - take (a liquid) into the mouth and swallow
   - a liquid that can be swallowed as refreshment or nourishment
plural: "drinks" [dɹɪŋkʰ]
Synonyms: 
    - beverage
    - liquid
    - sip
    - gulp
    - swallow
Antonyms: 
    - food
    - solid
    - edible
    - snack
    - meal
Parts Of Speech:  noun, verb
Conjugations:
    - 3rd sg present: drinks
```
