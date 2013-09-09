print(__doc__)
import sys
import json
import math
from pprint import pprint

##############################################################################
# Read Turker data file
def read_data(filename):
    json_data=open(filename)
    data = json.load(json_data)
    # pprint(data)
    json_data.close()
    return data

##############################################################################
# Parse labels
def parse_labels(workerid, seq, str):
    labels = []
    #print str
    split_str = str.split('\t')
    #print split_str
    for v in split_str:
        if v == '': continue
        split_v = v.split('@')
        label = {}
        label["workerid"] = workerid
        label["time"] = (seq-1)*54 + float(split_v[0])
        label["desc"] = split_v[1]
        labels.append(label)
    return labels


# Compute precision
def compute_precision(tp, fp):
    return (round(float(tp) / (tp + fp), 4))


# Compute recall
def compute_recall(tp, fn):
    return (round(float(tp) / (tp + fn), 4))


# Compute F-Score
def compute_f_score(precision, recall, beta=1):
    if precision == 0 or recall == 0:
        return 0.0000
    score = (1.0 + beta * beta) * (precision * recall) / (beta * beta * precision + recall)
    return round(score, 4)


'''
    Parameters
    turk_filename: without the epsilon part (e.g., s1_p.data)
    truth_filename (e.g., s1_p.truth.json)
'''
# how far away can the labels in turk and truth data be, to be considered correct?
window_size = int(sys.argv[3])

global_match = 0
global_fp = 0
global_fn = 0
global_turk_labels = 0
global_true_labels = 0

turk_filename = sys.argv[1]  # + '.final.json'
truth_filename = sys.argv[2]
# read and parse data
turk = read_data(turk_filename)
truth = read_data(truth_filename)

# for each turk input, compare against ground truth and compute measures

vidlist = turk.keys()
vidlist.sort()
# for index, vid in enumerate(vidlist):
#     vid = vid[:1] + "1" + vid[2:] # make the stage prefix match
#     vidlist[index] = vid[:1] + "1" + vid[2:]

for vid in vidlist:
    false_positive = 0
    false_negative = 0
    match = 0
    # make the stage prefix match
    if vid not in truth:
        print "no ground truth available", vid
        continue

    for cid in turk[vid]:
        label = turk[vid][cid]
        found = 0
        # consider noops now
        # if label["label"] == "noop":
        #     continue
        # Find a match between ground truth and turker input
        for true_id in truth[vid]:
            #print truth[vid]
            true_label = truth[vid][true_id]
            #print math.fabs(float(label["time"]) - float(true_label["time"]))
            if found is not 1 and "matched" not in true_label and math.fabs(float(label["time"]) - float(true_label["time"])) <= window_size:
                found += 1
                true_label["matched"] = True
                # correct += 1
                if vid == "s1_c01_v04":
                    print label["time"], true_label["time"]
                    print "Truth ", true_label["desc"]
                    print "Turker", label["label"].encode("utf-8")

        if found == 1:
            match += 1
        elif found > 1:
            print "not possible"
        elif found == 0:   # no match found for this label in the truth
            false_positive += 1

    for true_id in truth[vid]:
        if "matched" not in truth[vid][true_id]:
            false_negative += 1
    
    if match + false_positive == 0 or match + false_negative == 0:
        print "no matches for this video."
    else:    
        # print vid, "#Turk:", len(turk[vid]), " #True:", len(truth[vid]), " #Match:", match, " #FP:", false_positive, " #FN:", false_negative
        precision = compute_precision(match, false_positive)
        recall = compute_recall(match, false_negative)
        f1 = compute_f_score(precision, recall, 1)
        f2 = compute_f_score(precision, recall, 2)
        print "[%s]  Precision: %.4f  Recall:%.4f  F1:%.4f  F2:%.4f" % (vid, precision, recall, f1, f2), "turk:", len(turk[vid]), "true:", len(truth[vid]), "match:", match, "fp:", false_positive, "fn:", false_negative
    global_match += match
    global_fp += false_positive
    global_fn += false_negative
    global_turk_labels += len(turk[vid])
    global_true_labels += len(truth[vid])

# if (global_turk_labels == 0 or global_true_labels == 0):
#     print "eps:", eps, "0 labels"
# else:
# print "eps:", eps, "GLOBAL Precision:", float(global_match) / global_turk_labels, " GLOBAL Recall:", float(global_match) / global_true_labels
print "====================================="
global_precision = compute_precision(global_match, global_fp)
global_recall = compute_recall(global_match, global_fn)
global_f1 = compute_f_score(global_precision, global_recall, 1)
global_f2 = compute_f_score(global_precision, global_recall, 2)
print "GLOBAL Precision: %.4f Recall:%.4f  F1:%.4f  F2:%.4f" % (global_precision, global_recall, global_f1, global_f2)




