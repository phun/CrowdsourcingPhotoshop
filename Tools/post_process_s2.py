print(__doc__)
import sys

# stop words list from http://norm.al/2009/04/14/list-of-english-stop-words/
stop_words_list = [
"a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the"
]

##############################################################################
# Read Turker data file
def read_turk_data(filename):
    data = {}
    f = open(filename)
    lines = f.readlines()
    datum = {}
    for index, line in enumerate(lines):
        split_str = line.split('\t\t')
        # print split_str
        datum = {}
        datum["id"] = split_str[0]
        datum["workerid"] = split_str[1]
        datum["clusterid"] = split_str[2]
        datum["answer"] = split_str[3]
        datum["instruction"] = split_str[4]
        datum["order"] = split_str[5]
        datum["vid"] = split_str[6]
        datum["time"] = split_str[7]
        # print split_str[2].split('====')
        datum["labels"] = split_str[8]
        # datum["vid"] = split_str[5][:-4] # e.g., "s1_c02_v02" and "s04" from "s1_c02_v02_s04"
        # datum["labels"] = parse_labels(datum["workerid"], int(split_str[2][-2:]), split_str[3].rstrip('\n'))
        # if datum["vid"].startswith("s2_p03_v03"):
        #     print datum["instruction"] in datum["order"].split(',')
        #     print datum
        if datum["clusterid"] not in data:
            data[datum["clusterid"]] = []
        data[datum["clusterid"]].append(datum)
    f.close()
    return data


# Save JSON files of final clusters
def save_files(final_data):
    import json
    with open(sys.argv[1] + '.final.json', 'wb') as fp:
        #json.dump(final_data, fp)
        json.dump(final_data, fp, sort_keys=True, indent=4, separators=(',', ': '))


def get_filtered_label(line):
    word_list = [word for word in line.split()]
    final_list = []
    for word in word_list:
        if word not in stop_words_list:
            final_list.append(word)
    return " ".join(final_list)


# check if two labels should be considered identical
def is_same_label(label1, label2):
    # noop cannot be same with non-noop
    if (label1 == "noop" and label2 != "noop") or (label1 != "noop" and label2 == "noop"):
        return [False, "", "noop"]
    if label1 == label2:
        return [True, label1, "identical"]
    l1 = label1.lower()
    l2 = label2.lower()
    if l1 == l2:
        return [True, l1, "case"]
    fl1 = get_filtered_label(l1)
    fl2 = get_filtered_label(l2)
    if get_filtered_label(l1) == get_filtered_label(l2):
        return [True, get_filtered_label(l1), "stopword"]
    # TODO: string comparison, sentence analysis
    # import difflib
    # sim_score = difflib.SequenceMatcher(None, fl1,fl2).ratio()
    import jellyfish
    sim_score = jellyfish.jaro_winkler(fl1, fl2)
    if sim_score >= 0.9:
        # print "fuzzy", sim_score,
        # print l1, "===", l2  # fl1, "===", fl2, "|||",
        final_label = max([l1, l2], key=len)
        return [True, final_label, "sim"]
    # print "diff", sim_score, l1, "===", l2
    return [False, "", ""]


def compare_int_keys(x, y):
    x = int(x)
    y = int(y)
    return cmp(x,y)


''' for the given Turker input, return the Turker's answer in text
'''
def getLabel(label):
    answer = ""
    if label["answer"] == "@":
        answer = label["instruction"]
    elif label["answer"] == "noop":
        answer = "noop"
    else:
        label_list = label["labels"].split("====")
        answer = label_list[int(label["answer"])].rstrip('\n')
    return answer


''' 
    Post-Process stage 2 results
    Usage 
        python dbscan.py [turk_filename]
    Parameters
    - turk_filename: e.g., "s2.data"
'''
turk_filename = sys.argv[1]
# truth_filename = sys.argv[2]
# read and parse data
clusters = read_turk_data(turk_filename)
# truth = read_truth_data(truth_filename)
cluster_list = clusters.keys()
cluster_list = sorted(cluster_list, cmp=compare_int_keys)

# fp = open(turk_filename + ".parsed", 'wb')

final_data = {}

count_noop = 0
count_unanimous = 0
count_unanimous_noop_included = 0
count_unanimous_custom_included = 0
count_majority = 0
count_majority_noop_included = 0
count_majority_dup_string = 0
count_majority_custom_included = 0
count_diverse = 0
count_diverse_noop_included = 0
count_diverse_dup_string = 0
count_diverse_custom_included = 0
count_else = 0

for cid in cluster_list:
    cluster = clusters[cid]
    l1 = cluster[0]
    l2 = cluster[1]
    l3 = cluster[2]
    final_label = ""
    # print l1["vid"], cid
    if l1["answer"] == l2["answer"] == l3["answer"]:
        # print "[unanimous]", getLabel(l1)
        final_label = getLabel(l1)
        count_unanimous += 1
        if l1["answer"] == "noop":
            count_unanimous_noop_included += 1
        if l1["answer"] == "@":
            count_unanimous_custom_included += 1
    elif (l1["answer"] == l2["answer"]) or (l2["answer"] == l3["answer"]) or (l3["answer"] == l1["answer"]):
        if l1["answer"] == l2["answer"]:
            if l1["answer"] == "@":
                count_majority_custom_included += 1
            if is_same_label(getLabel(l1), getLabel(l3))[0]:
                count_majority_dup_string += 1
            answer_label = l1
            if l1["answer"] == "noop":
                count_majority_noop_included += 1    
        elif l2["answer"] == l3["answer"]:
            if l2["answer"] == "@":
                count_majority_custom_included += 1
            if is_same_label(getLabel(l2), getLabel(l1))[0]:
                count_majority_dup_string += 1            
            answer_label = l2
            if l2["answer"] == "noop":
                count_majority_noop_included += 1    
        elif l3["answer"] == l1["answer"]:
            if l3["answer"] == "@":
                count_majority_custom_included += 1
            if is_same_label(getLabel(l3), getLabel(l2))[0]:
                count_majority_dup_string += 1
            answer_label = l3
            if l3["answer"] == "noop":
                count_majority_noop_included += 1
        # print "[majority]", getLabel(answer_label)
        final_label = getLabel(answer_label)
        count_majority += 1
    elif (l1["answer"] != l2["answer"]) and (l2["answer"] != l3["answer"]) and (l3["answer"] != l1["answer"]):
        # print "[diverse]", getLabel(l1), "==", getLabel(l2), "==", getLabel(l3)
        if l1["answer"] == "@" or l2["answer"] == "@" or l3["answer"] == "@":
            count_diverse_custom_included += 1      
        s12 = is_same_label(getLabel(l1), getLabel(l2))
        s23 = is_same_label(getLabel(l2), getLabel(l3))
        s31 = is_same_label(getLabel(l3), getLabel(l1))
        if s12[0]:
            count_diverse_dup_string += 1
            final_label = s12[1]
        elif s23[0]:
            count_diverse_dup_string += 1
            final_label = s23[1]
        elif s31[0]:
            count_diverse_dup_string += 1
            final_label = s31[1]
        else:
            # let's use the longest string if we cannot resolve
            # final_label = max([getLabel(l1), getLabel(l2), getLabel(l3)], key=len)
            print "no match", l1["answer"], l2["answer"], l3["answer"]
            final_label = "noop"

        if l1["answer"] == "noop" or l2["answer"] == "noop" or l3["answer"] == "noop":
            count_diverse_noop_included += 1
            final_label = "noop"
        
        count_diverse += 1
    else:
        print "something else"
        count_else += 1

    count_noop = count_unanimous_noop_included + count_majority_noop_included + count_diverse_noop_included

    vid = l1["vid"][:-4]
    # make the stage prefix match
    vid = vid[:1] + "1" + vid[2:]
    if vid not in final_data:
        final_data[vid] = {}
    final_data[vid][cid] = {"cluster_id": cid, "time": l1["time"], "label": final_label}
    # datum["id"] = split_str[0]
    # datum["workerid"] = split_str[1]
    # datum["clusterid"] = split_str[2]
    # datum["answer"] = split_str[3]
    # datum["instruction"] = split_str[4]
    # datum["order"] = split_str[5]
    # datum["vid"] = split_str[6]
    # # print split_str[2].split('====')
    # datum["labels"] = split_str[7]

    # print "\n" 
    # sorted_turk = sorted(turk[cid], key=lambda tup: tup["time"])
    # print vid, "Turk:", len(turk[vid]), "Truth:", len(truth[vid])#, data[vid]
    # only grab time information from the data

    # fp.write("===" + vid + " " + str(eps) + "===\n")

    # cluster_data = {}
    # for (i, label) in enumerate(label_turk_list):
    #     key = str(clusters[i])
    #     entry = {'cluster_id': key, 'time': label, 'workerid': sorted_turk[i]["workerid"], 'desc': sorted_turk[i]["desc"]}
    #     if key not in cluster_data:
    #         cluster_data[key] = []
    #     cluster_data[key].append(entry)
    #     line = str(label) + " (" + clusters[i] + ") " + sorted_turk[i]["workerid"] + " " + sorted_turk[i]["desc"]
    #     fp.write(line + "\n")
    #     print label, "(", clusters[i], ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]

    # final_data[vid] = {}
    # for cid in cluster_data:
    #     final_data[vid][cid] = {'cluster_id': cid, 'time': getClusterTime(cluster_data[cid], cid), 'points_turk': cluster_data[cid]}
    #     # print "---"
    #     # print final_data[vid][cid]

save_files(final_data)
# print final_data
# print len(final_data)
print "STATS"
print "unanimous:", count_unanimous, "noop", count_unanimous_noop_included, "custom", count_unanimous_custom_included
print "majority:", count_majority, "noop", count_majority_noop_included, "dup", count_majority_dup_string, "custom", count_majority_custom_included
print "diverse:", count_diverse, "noop", count_diverse_noop_included, "dup", count_diverse_dup_string, "custom", count_diverse_custom_included
print "noop:", count_noop



