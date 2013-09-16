import sys

SIM_THRESHOLD = 0.4

# stop words list from http://norm.al/2009/04/14/list-of-english-stop-words/
stop_words_list = [
"a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the",
]


# remove stop words from a label
def get_filtered_label(line):
    word_list = [word for word in line.split()]
    final_list = []
    for word in word_list:
    	# removing non-characters (numbers, special characters)
    	new_word = ''.join([i for i in word if i.isalpha()])
        if new_word is not '' and new_word not in stop_words_list:
            final_list.append(new_word)
    return " ".join(final_list)


# check if two labels should be considered identical
def is_same_label(label1, label2):
    # noop cannot be same with non-noop
    if (label1 == "noop" and label2 != "noop") or (label1 != "noop" and label2 == "noop"):
        return [False, "", "noop", 0]
    if label1 == label2:
        return [True, label1, "identical", 1]
    l1 = label1.lower()
    l2 = label2.lower()
    if l1 == l2:
        return [True, l1, "case", 1]
    fl1 = get_filtered_label(l1)
    fl2 = get_filtered_label(l2)
    if get_filtered_label(l1) == get_filtered_label(l2):
        return [True, get_filtered_label(l1), "stopword", 1]
    # TODO: string comparison, sentence analysis
    # import difflib
    # sim_score = difflib.SequenceMatcher(None, fl1,fl2).ratio()
    import jellyfish
    sim_score = jellyfish.jaro_winkler(fl1, fl2)
    if sim_score >= SIM_THRESHOLD:
        # print "fuzzy", sim_score,
        # print "[fuzzy]", sim_score, l1, "===", l2  # fl1, "===", fl2, "|||",
        final_label = max([l1, l2], key=len)
        return [True, final_label, "sim", sim_score]
    # print "diff", sim_score, l1, "===", l2
    return [False, "", "", sim_score]