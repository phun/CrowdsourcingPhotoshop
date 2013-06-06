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





'''
    Parameters
    turk_filename: without the epsilon part (e.g., s1_p.data)
    truth_filename (e.g., s1_p.truth)
'''
# how far away can the labels in turk and truth data be, to be considered correct?
window_size = int(sys.argv[3])

for i in range(1, 30):
    eps = i * 0.01
    global_match = 0
    global_turk_labels = 0
    global_true_labels = 0


    turk_filename = sys.argv[1] + '.' + str(eps) + '.final.json'
    truth_filename = sys.argv[2]
    # read and parse data
    turk = read_data(turk_filename)
    truth = read_data(truth_filename)

    # for each turk input, compare against ground truth and compute measures

    vidlist = turk.keys()
    vidlist.sort()

    for vid in vidlist:
        # print vid
        false_positive = 0
        false_negative = 0
        match = 0
        if vid not in truth:
            continue

        for lid in turk[vid]:
            label = turk[vid][lid]
            found = 0
            #find something close to this label
            
            for true_id in truth[vid]:
                #print truth[vid]
                true_label = truth[vid][true_id]
                #print math.fabs(float(label["time"]) - float(true_label["time"]))
                if math.fabs(float(label["time"]) - float(true_label["time"])) <= window_size:
                    found += 1
                    # correct += 1
                    #print label["time"], true_label["time"], true_label["desc"]
            # print found
            if found > 0:
                match += 1
            # if found == 1:
            #     match += 1
            # elif found > 1:
            #     false_negative += 1
            # else:
            #     false_positive += 1

        # print vid, "#Turk:", len(turk[vid]), " #True:", len(truth[vid]), " #Match:", match, " #FP:", false_positive, " #FN:", false_negative
        # print "Precision:", float(match) / len(turk[vid]), " Recall:", float(match) / len(truth[vid])
        global_match += match
        global_turk_labels += len(turk[vid])
        global_true_labels += len(truth[vid])

    print "eps:", eps, "GLOBAL Precision:", float(global_match) / global_turk_labels, " GLOBAL Recall:", float(global_match) / global_true_labels

# final_data = {}
# for vid in vidlist:
#     print "\n" 
#     sorted_turk = sorted(turk[vid], key=lambda tup: tup["time"])

#     # if (vid == "s1_p03_v03"):
#     #     print "ORG:"
#     #     for label in turk[vid]:
#     #         print label["time"], label["desc"]
#     #     print "SORTED:"
#     #     for label in sorted_turk:
#     #         print label["time"], label["desc"]

#     print vid, "Turk:", len(turk[vid]), "Truth:", len(truth[vid])#, data[vid]
#     # only grab time information from the data
#     label_turk_list = []
#     label_truth_list = []
#     for label in turk[vid]:
#         label_turk_list.append(label["time"])
#     for label in truth[vid]:
#         label_truth_list.append(label["time"])
#     label_turk_list.sort()
#     label_truth_list.sort()
#     # format for DBSCAN
    
#     labels_turk = format_data(label_turk_list)
#     #labels_truth = label_truth_list #format_data(label_truth_list)
#     # now run DBSCAN
#     clusters = run_dbscan(labels_turk, label_turk_list, label_truth_list)


#     # if (vid == "s1_p03_v03"):
#     print "TRUE:"
#     for label in truth[vid]:
#         print label["time"], label["desc"]
#     print "==="
#     print "TURK:"
#     cluster_data = {}
#     for (i, label) in enumerate(label_turk_list):
#         key = str(clusters[i])
#         entry = {'cluster_id': key, 'time': label, 'workerid': sorted_turk[i]["workerid"], 'desc': sorted_turk[i]["desc"]}
#         if key in cluster_data:
#             cluster_data[key].append(entry)
#         else:
#             cluster_data[key] = []
#         #print cluster_data[key]
#         print label, "(", int(clusters[i]), ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]

#     final_data[vid] = {}
#     for cid in cluster_data:
#         #print cid, cluster_data[cid]
#         final_data[vid][cid] = {'cluster_id': cid, 'time': getClusterTime(cluster_data[cid], cid), 'points_turk': cluster_data[cid]}
#         print "---"
#         print final_data[vid][cid]
#     # for cluster in cluster_data:
#     #     final_data[vid] = {'cluster_id': cluster["cluster_id"], 'time': getClusterTime(cluster), 'points_turk': cluster}

#     import json
#     with open(sys.argv[2] + '.final.json', 'wb') as fp:
#         json.dump(final_data, fp)




